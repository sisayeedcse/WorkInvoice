<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $purchaseOrders = $query->latest()->get();

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        return view('purchase-orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_number'             => 'nullable|string|max:255|unique:purchase_orders,po_number',
            'supplier_name'         => 'required|string|max:255',
            'date'                  => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.item_name'     => 'required|string',
            'items.*.date'          => 'nullable|date',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $itemsData  = $request->items;
            $subtotal   = collect($itemsData)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $discount   = (float) ($request->discount ?? 0);
            $tax        = (float) ($request->tax ?? 0);
            $grandTotal = $subtotal - $discount + (($subtotal - $discount) * $tax / 100);

            $po = PurchaseOrder::create([
                'po_number'        => $request->filled('po_number') ? trim((string) $request->po_number) : PurchaseOrder::generateNumber(),
                'supplier_name'    => $request->supplier_name,
                'supplier_phone'   => $request->supplier_phone,
                'supplier_email'   => $request->supplier_email,
                'supplier_address' => $request->supplier_address,
                'date'             => $request->date,
                'delivery_date'    => $request->delivery_date,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'tax'              => $tax,
                'grand_total'      => $grandTotal,
                'status'           => 'draft',
                'notes'            => $request->notes,
                'terms'            => $request->terms,
                'order_id'         => $request->order_id ?? null,
                'created_by'       => auth()->id(),
            ]);

            foreach ($itemsData as $index => $itemData) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'item_name'         => $itemData['item_name'],
                    'description'       => $itemData['description'] ?? null,
                    'line_date'         => $itemData['date'] ?? null,
                    'quantity'          => $itemData['quantity'],
                    'unit'              => $itemData['unit'] ?? 'Unit',
                    'unit_price'        => $itemData['unit_price'],
                    'total'             => $itemData['quantity'] * $itemData['unit_price'],
                    'sort_order'        => $index,
                ]);
            }
        });

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase order created.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        return view('purchase-orders.edit', compact('purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update([
            'supplier_name'    => $request->supplier_name,
            'supplier_phone'   => $request->supplier_phone,
            'supplier_email'   => $request->supplier_email,
            'supplier_address' => $request->supplier_address,
            'date'             => $request->date,
            'delivery_date'    => $request->delivery_date,
            'status'           => $request->status ?? $purchaseOrder->status,
            'notes'            => $request->notes,
            'discount'         => (float) ($request->discount ?? 0),
        ]);

        // Recalculate grand total with updated discount
        $taxAmount = ($purchaseOrder->subtotal - (float) ($request->discount ?? 0)) * $purchaseOrder->tax / 100;
        $purchaseOrder->update([
            'grand_total' => $purchaseOrder->subtotal - (float) ($request->discount ?? 0) + $taxAmount,
        ]);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase order updated.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase order deleted.');
    }

    public function pdf(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items');
        $company = config('company');

        // Load DB-overridden company settings and logo
        $dbSettings = ['name','owner','address','phone','email','tagline','currency','currency_symbol'];
        foreach ($dbSettings as $key) {
            $val = \App\Models\Setting::get("company_{$key}") ?? \App\Models\Setting::get($key);
            if ($val) $company[$key] = $val;
        }
        $logoPath = \App\Models\Setting::get('company_logo');
        $company['logo_base64'] = null;
        if ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
            $file = \Illuminate\Support\Facades\Storage::disk('public')->get($logoPath);
            $mime = mime_content_type(\Illuminate\Support\Facades\Storage::disk('public')->path($logoPath));
            $company['logo_base64'] = 'data:' . $mime . ';base64,' . base64_encode($file);
        }

        $pdf = Pdf::loadView('pdf.purchase-order', compact('purchaseOrder', 'company'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($purchaseOrder->po_number . '.pdf');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate(['status' => 'required|in:draft,sent,received,cancelled']);
        $purchaseOrder->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }
}
