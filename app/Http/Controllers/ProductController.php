<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::whereNull('deleted_at');

        // Filter by item type
        if ($request->filled('type')) {
            $query->where('item_type', $request->type);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereColumn('stock_quantity', '<=', 'reorder_level')
                      ->whereNotNull('reorder_level');
            } elseif ($request->stock_status === 'out') {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Search by name or SKU
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
            });
        }

        $products = $query->latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'sku'              => 'nullable|string|max:50|unique:items,sku',
            'description'      => 'nullable|string',
            'item_type'        => 'required|in:trading,manufactured,raw_material,service',
            'default_price'    => 'required|numeric|min:0',
            'cost_price'       => 'nullable|numeric|min:0',
            'unit'             => 'required|string|max:50',
            'category'         => 'nullable|string|max:100',
            'track_inventory'  => 'boolean',
            'stock_quantity'   => 'nullable|numeric|min:0',
            'reorder_level'    => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $item = Item::create($validated);

            // If initial stock is provided, create a stock movement
            if ($request->filled('stock_quantity') && $request->stock_quantity > 0) {
                StockMovement::create([
                    'item_id' => $item->id,
                    'movement_type' => 'in',
                    'quantity' => $request->stock_quantity,
                    'balance_after' => $request->stock_quantity,
                    'reference_type' => 'adjustment',
                    'notes' => 'Initial stock',
                    'movement_date' => now(),
                    'created_by' => auth()->id(),
                ]);
            }
        });

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    public function show(Item $product)
    {
        $product->load(['stockMovements' => function ($query) {
            $query->latest('movement_date')->limit(100);
        }]);

        return view('products.show', compact('product'));
    }

    public function edit(Item $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Item $product)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'sku'              => 'nullable|string|max:50|unique:items,sku,' . $product->id,
            'description'      => 'nullable|string',
            'item_type'        => 'required|in:trading,manufactured,raw_material,service',
            'default_price'    => 'required|numeric|min:0',
            'cost_price'       => 'nullable|numeric|min:0',
            'unit'             => 'required|string|max:50',
            'category'         => 'nullable|string|max:100',
            'track_inventory'  => 'boolean',
            'reorder_level'    => 'nullable|numeric|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Item $product)
    {
        try {
            // Check if product has stock movements
            if ($product->stockMovements()->count() > 0) {
                return redirect()->route('products.index')
                    ->with('error', 'Cannot delete product with stock movement history. Archive it instead.');
            }

            $product->delete();
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')
                ->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    public function adjustStock(Item $product)
    {
        return view('products.adjust-stock', compact('product'));
    }

    public function updateStock(Request $request, Item $product)
    {
        $request->validate([
            'new_quantity' => 'required|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        try {
            $product->adjustStock($request->new_quantity, $request->notes);

            return redirect()->route('products.show', $product)
                ->with('success', 'Stock adjusted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to adjust stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    // AJAX search for POS - RETAIL PRODUCTS ONLY
    public function search(Request $request)
    {
        // Only return trading (retail) items for POS
        $items = Item::where('is_active', true)
            ->where('track_inventory', true)
            ->where('item_type', 'trading') // CRITICAL: Restrict to retail products only
            ->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('sku', 'like', "%{$request->q}%");
            })
            ->with(['stockMovements' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->select('id', 'name', 'sku', 'default_price', 'stock_quantity', 'unit', 'item_type')
            ->take(20)
            ->get();

        return response()->json($items);
    }
}
