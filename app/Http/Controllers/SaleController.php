<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Item;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::whereNull('deleted_at')->with(['customer', 'items', 'creator']);

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('sale_date', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->latest('sale_date')->latest('id')->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $products = Item::where('is_active', true)
            ->where('track_inventory', true)
            ->where('item_type', '!=', 'service')
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date'         => 'required|date',
            'customer_id'       => 'nullable|exists:customers,id',
            'customer_name'     => 'nullable|string|max:255',
            'items'             => 'required|array|min:1',
            'items.*.item_id'   => 'required|exists:items,id',
            'items.*.quantity'  => 'required|numeric|min:0.001',
            'items.*.unit_price'=> 'required|numeric|min:0',
            'discount'          => 'nullable|numeric|min:0',
            'tax'               => 'nullable|numeric|min:0',
            'payment_method'    => 'required|in:cash,bank_transfer,online,cheque,other',
            'payment_reference' => 'nullable|string|max:100',
            'notes'             => 'nullable|string',
        ]);

        try {
            // Validate stock availability and product type for all items first
            foreach ($request->items as $itemData) {
                $product = Item::findOrFail($itemData['item_id']);

                // BUSINESS RULE: Traditional sales form also restricted to retail
                if ($product->item_type !== 'trading') {
                    return back()->withErrors([
                        'items' => "Only retail products can be sold. '{$product->name}' is a warehouse item. Use Projects for custom fabrications."
                    ])->withInput();
                }

                if ($product->track_inventory && $product->stock_quantity < $itemData['quantity']) {
                    return back()->withErrors([
                        'items' => "Insufficient stock for {$product->name}. Available: {$product->stock_quantity} {$product->unit}"
                    ])->withInput();
                }
            }

            $sale = DB::transaction(function () use ($request, $validated) {
                // Calculate totals
                $subtotal = 0;
                foreach ($request->items as $item) {
                    $subtotal += $item['quantity'] * $item['unit_price'];
                }

                $discount = (float) ($request->discount ?? 0);
                $tax = (float) ($request->tax ?? 0);
                $grandTotal = $subtotal - $discount + $tax;

                // Create sale
                $sale = Sale::create([
                    'sale_number'       => Sale::generateNumber(),
                    'customer_id'       => $request->customer_id,
                    'customer_name'     => $request->customer_name,
                    'sale_date'         => $request->sale_date,
                    'subtotal'          => $subtotal,
                    'discount'          => $discount,
                    'tax'               => $tax,
                    'grand_total'       => $grandTotal,
                    'payment_method'    => $request->payment_method,
                    'payment_reference' => $request->payment_reference,
                    'notes'             => $request->notes,
                    'created_by'        => auth()->id(),
                ]);

                // Create sale items and reduce stock
                $sortOrder = 0;
                foreach ($request->items as $itemData) {
                    $product = Item::findOrFail($itemData['item_id']);

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'item_id'    => $product->id,
                        'item_name'  => $product->name,
                        'quantity'   => $itemData['quantity'],
                        'unit'       => $product->unit,
                        'unit_price' => $itemData['unit_price'],
                        'total'      => $itemData['quantity'] * $itemData['unit_price'],
                        'sort_order' => $sortOrder++,
                    ]);

                    // Reduce stock
                    if ($product->track_inventory) {
                        $product->removeStock(
                            $itemData['quantity'],
                            'sale',
                            $sale->id,
                            "Sale {$sale->sale_number}"
                        );
                    }
                }

                return $sale;
            });

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale completed successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to complete sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function posCheckout(Request $request)
    {
        $validated = $request->validate([
            'customer_id'       => 'nullable|exists:customers,id',
            'customer_name'     => 'nullable|string|max:255',
            'items'             => 'required|array|min:1',
            'items.*.item_id'   => 'required|exists:items,id',
            'items.*.quantity'  => 'required|numeric|min:0.001',
            'payment_method'    => 'required|in:cash,bank_transfer,online,cheque,other',
            'notes'             => 'nullable|string',
            'print_receipt'     => 'nullable|boolean',
        ]);

        try {
            foreach ($validated['items'] as $itemData) {
                $product = Item::findOrFail($itemData['item_id']);

                // BUSINESS RULE: POS can only sell trading (retail) items
                if ($product->item_type !== 'trading') {
                    return response()->json([
                        'message' => "Only retail products can be sold via POS. '{$product->name}' is a warehouse item and must be managed through Projects."
                    ], 422);
                }

                if ($product->track_inventory && $product->stock_quantity < $itemData['quantity']) {
                    return response()->json([
                        'message' => "Insufficient stock for {$product->name}. Available: {$product->stock_quantity} {$product->unit}"
                    ], 422);
                }
            }

            $sale = DB::transaction(function () use ($validated, $request) {
                $subtotal = 0;
                foreach ($validated['items'] as $item) {
                    $product = Item::findOrFail($item['item_id']);
                    $subtotal += $item['quantity'] * $product->default_price;
                }

                $sale = Sale::create([
                    'sale_number'       => Sale::generateNumber(),
                    'customer_id'       => $validated['customer_id'] ?? null,
                    'customer_name'     => $validated['customer_name'] ?? null,
                    'sale_date'         => now()->toDateString(),
                    'subtotal'          => $subtotal,
                    'discount'          => 0,
                    'tax'               => 0,
                    'grand_total'       => $subtotal,
                    'payment_method'    => $validated['payment_method'],
                    'payment_reference' => null,
                    'notes'             => $validated['notes'] ?? null,
                    'created_by'        => auth()->id(),
                ]);

                $sortOrder = 0;
                foreach ($validated['items'] as $itemData) {
                    $product = Item::findOrFail($itemData['item_id']);
                    $lineTotal = $itemData['quantity'] * $product->default_price;

                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'item_id'    => $product->id,
                        'item_name'  => $product->name,
                        'quantity'   => $itemData['quantity'],
                        'unit'       => $product->unit,
                        'unit_price' => $product->default_price,
                        'total'      => $lineTotal,
                        'sort_order' => $sortOrder++,
                    ]);

                    if ($product->track_inventory) {
                        $product->removeStock(
                            $itemData['quantity'],
                            'sale',
                            $sale->id,
                            "Sale {$sale->sale_number}"
                        );
                    }
                }

                return $sale;
            });

            return response()->json([
                'message' => 'Sale completed successfully.',
                'sale_id' => $sale->id,
                'sale_url' => route('sales.show', $sale),
                'receipt_url' => route('sales.receipt', $sale),
                'print_receipt' => (bool) ($validated['print_receipt'] ?? false),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to complete sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'items.item', 'creator']);

        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::transaction(function () use ($sale) {
                // Restore stock for all items
                foreach ($sale->items as $saleItem) {
                    if ($saleItem->item_id) {
                        $product = Item::find($saleItem->item_id);
                        if ($product && $product->track_inventory) {
                            $product->addStock(
                                $saleItem->quantity,
                                'adjustment',
                                null,
                                "Sale {$sale->sale_number} voided"
                            );
                        }
                    }
                }

                $sale->delete();
            });

            return redirect()->route('sales.index')
                ->with('success', 'Sale voided and stock restored successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sales.index')
                ->with('error', 'Failed to void sale: ' . $e->getMessage());
        }
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['customer', 'items']);

        // Get company settings
        $companySettings = Setting::where('group', 'company')->pluck('value', 'key');
        $company = [
            'name'    => $companySettings['name'] ?? config('app.name'),
            'address' => $companySettings['address'] ?? '',
            'phone'   => $companySettings['phone'] ?? '',
            'email'   => $companySettings['email'] ?? '',
        ];

        $pdf = Pdf::loadView('pdf.receipt', compact('sale', 'company'))
            ->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width

        $sale->update(['receipt_printed' => true]);

        return $pdf->download('receipt-' . $sale->sale_number . '.pdf');
    }

    public function dailySummary()
    {
        $todaySales = Sale::whereDate('sale_date', today())
            ->with('items')
            ->get();

        $summary = [
            'total_sales'   => $todaySales->sum('grand_total'),
            'sales_count'   => $todaySales->count(),
            'items_sold'    => $todaySales->sum(function ($sale) {
                return $sale->items->sum('quantity');
            }),
            'cash_sales'    => $todaySales->where('payment_method', 'cash')->sum('grand_total'),
            'bank_sales'    => $todaySales->where('payment_method', 'bank_transfer')->sum('grand_total'),
            'other_sales'   => $todaySales->whereNotIn('payment_method', ['cash', 'bank_transfer'])->sum('grand_total'),
        ];

        return response()->json($summary);
    }
}
