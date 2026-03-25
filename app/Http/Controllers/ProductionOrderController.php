<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\ProductionItem;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductionOrder::whereNull('deleted_at')
            ->with(['finishedItem', 'materials', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $productions = $query->latest('production_date')->latest('id')->get();

        return view('production-orders.index', compact('productions'));
    }

    public function create()
    {
        // Get manufactured products for finished goods
        $finishedProducts = Item::where('is_active', true)
            ->where('item_type', 'manufactured')
            ->orderBy('name')
            ->get();

        // Get raw materials and trading items for materials
        $materials = Item::where('is_active', true)
            ->whereIn('item_type', ['raw_material', 'trading'])
            ->orderBy('name')
            ->get();

        return view('production-orders.create', compact('finishedProducts', 'materials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'finished_item_id'              => 'required|exists:items,id',
            'quantity_to_produce'           => 'required|numeric|min:0.001',
            'production_date'               => 'required|date',
            'notes'                         => 'nullable|string',
            'materials'                     => 'required|array|min:1',
            'materials.*.item_id'           => 'required|exists:items,id',
            'materials.*.quantity_required' => 'required|numeric|min:0.001',
        ]);

        // Validate finished item is manufactured type
        $finishedItem = Item::findOrFail($request->finished_item_id);
        if ($finishedItem->item_type !== 'manufactured') {
            return back()->withErrors([
                'finished_item_id' => 'Selected item must be a manufactured product.'
            ])->withInput();
        }

        try {
            $production = DB::transaction(function () use ($request, $validated) {
                // Create production order
                $production = ProductionOrder::create([
                    'production_number'   => ProductionOrder::generateNumber(),
                    'finished_item_id'    => $request->finished_item_id,
                    'quantity_to_produce' => $request->quantity_to_produce,
                    'production_date'     => $request->production_date,
                    'status'              => 'pending',
                    'notes'               => $request->notes,
                    'created_by'          => auth()->id(),
                ]);

                // Create production items (materials needed)
                $sortOrder = 0;
                foreach ($request->materials as $materialData) {
                    $material = Item::findOrFail($materialData['item_id']);

                    ProductionItem::create([
                        'production_order_id' => $production->id,
                        'item_id'             => $material->id,
                        'item_name'           => $material->name,
                        'quantity_required'   => $materialData['quantity_required'],
                        'unit'                => $material->unit,
                        'unit_cost'           => $material->cost_price,
                        'total_cost'          => $materialData['quantity_required'] * ($material->cost_price ?? 0),
                        'sort_order'          => $sortOrder++,
                    ]);
                }

                return $production;
            });

            return redirect()->route('production-orders.show', $production)
                ->with('success', 'Production order created successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create production order: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ProductionOrder $productionOrder)
    {
        $productionOrder->load(['finishedItem', 'materials.item', 'creator']);

        return view('production-orders.show', compact('productionOrder'));
    }

    public function edit(ProductionOrder $productionOrder)
    {
        // Only allow editing if status is pending
        if ($productionOrder->status !== 'pending') {
            return redirect()->route('production-orders.show', $productionOrder)
                ->with('error', 'Cannot edit production order that is not in pending status.');
        }

        $productionOrder->load('materials');

        $finishedProducts = Item::where('is_active', true)
            ->where('item_type', 'manufactured')
            ->orderBy('name')
            ->get();

        $materials = Item::where('is_active', true)
            ->whereIn('item_type', ['raw_material', 'trading'])
            ->orderBy('name')
            ->get();

        return view('production-orders.edit', compact('productionOrder', 'finishedProducts', 'materials'));
    }

    public function update(Request $request, ProductionOrder $productionOrder)
    {
        // Only allow editing if status is pending
        if ($productionOrder->status !== 'pending') {
            return redirect()->route('production-orders.show', $productionOrder)
                ->with('error', 'Cannot edit production order that is not in pending status.');
        }

        $validated = $request->validate([
            'finished_item_id'              => 'required|exists:items,id',
            'quantity_to_produce'           => 'required|numeric|min:0.001',
            'production_date'               => 'required|date',
            'notes'                         => 'nullable|string',
            'materials'                     => 'required|array|min:1',
            'materials.*.item_id'           => 'required|exists:items,id',
            'materials.*.quantity_required' => 'required|numeric|min:0.001',
        ]);

        try {
            DB::transaction(function () use ($request, $productionOrder) {
                // Update production order
                $productionOrder->update([
                    'finished_item_id'    => $request->finished_item_id,
                    'quantity_to_produce' => $request->quantity_to_produce,
                    'production_date'     => $request->production_date,
                    'notes'               => $request->notes,
                ]);

                // Delete and recreate materials
                $productionOrder->materials()->delete();

                $sortOrder = 0;
                foreach ($request->materials as $materialData) {
                    $material = Item::findOrFail($materialData['item_id']);

                    ProductionItem::create([
                        'production_order_id' => $productionOrder->id,
                        'item_id'             => $material->id,
                        'item_name'           => $material->name,
                        'quantity_required'   => $materialData['quantity_required'],
                        'unit'                => $material->unit,
                        'unit_cost'           => $material->cost_price,
                        'total_cost'          => $materialData['quantity_required'] * ($material->cost_price ?? 0),
                        'sort_order'          => $sortOrder++,
                    ]);
                }
            });

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('success', 'Production order updated successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update production order: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(ProductionOrder $productionOrder)
    {
        // Only allow deletion if status is pending
        if ($productionOrder->status !== 'pending') {
            return redirect()->route('production-orders.index')
                ->with('error', 'Cannot delete production order that is not in pending status.');
        }

        try {
            $productionOrder->delete();
            return redirect()->route('production-orders.index')
                ->with('success', 'Production order deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('production-orders.index')
                ->with('error', 'Failed to delete production order: ' . $e->getMessage());
        }
    }

    public function start(ProductionOrder $productionOrder)
    {
        if ($productionOrder->status !== 'pending') {
            return back()->with('error', 'Production order must be in pending status to start.');
        }

        $productionOrder->update(['status' => 'in_progress']);

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('success', 'Production started successfully.');
    }

    public function complete(ProductionOrder $productionOrder)
    {
        if (!in_array($productionOrder->status, ['pending', 'in_progress'])) {
            return back()->with('error', 'Production order cannot be completed from current status.');
        }

        try {
            DB::transaction(function () use ($productionOrder) {
                // Validate raw material availability
                foreach ($productionOrder->materials as $material) {
                    $item = Item::findOrFail($material->item_id);

                    if ($item->track_inventory && $item->stock_quantity < $material->quantity_required) {
                        throw new \Exception(
                            "Insufficient stock for {$item->name}. Required: {$material->quantity_required} {$item->unit}, Available: {$item->stock_quantity} {$item->unit}"
                        );
                    }
                }

                // Consume raw materials (reduce stock)
                foreach ($productionOrder->materials as $material) {
                    $item = Item::findOrFail($material->item_id);

                    if ($item->track_inventory) {
                        $item->removeStock(
                            $material->quantity_required,
                            'production',
                            $productionOrder->id,
                            "Used in production {$productionOrder->production_number}"
                        );
                    }
                }

                // Produce finished goods (increase stock)
                $finishedItem = $productionOrder->finishedItem;
                if ($finishedItem->track_inventory) {
                    $finishedItem->addStock(
                        $productionOrder->quantity_to_produce,
                        'production',
                        $productionOrder->id,
                        "Produced via {$productionOrder->production_number}"
                    );
                }

                // Update production status
                $productionOrder->update([
                    'status'            => 'completed',
                    'quantity_produced' => $productionOrder->quantity_to_produce,
                    'completion_date'   => now(),
                ]);
            });

            return redirect()->route('production-orders.show', $productionOrder)
                ->with('success', 'Production completed successfully. Stock updated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, ProductionOrder $productionOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        $productionOrder->update(['status' => $request->status]);

        return redirect()->route('production-orders.show', $productionOrder)
            ->with('success', 'Production order status updated.');
    }
}
