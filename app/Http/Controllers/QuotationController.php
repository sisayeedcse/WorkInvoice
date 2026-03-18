<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::whereNull('deleted_at')->with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotations = $query->latest()->get();

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $items     = Item::where('is_active', true)->orderBy('name')->get();
        $terms     = config('company.terms');

        return view('quotations.create', compact('customers', 'items', 'terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_number'      => 'nullable|string|max:255|unique:quotations,quotation_number',
            'customer_id'           => 'required|exists:customers,id',
            'date'                  => 'required|date',
            'valid_until'           => 'nullable|date',
            'prepared_by'           => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.item_name'     => 'required|string',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        $quotationNumber = trim((string) $request->input('quotation_number', ''));

        DB::transaction(function () use ($request, $quotationNumber) {
            $itemsData = $request->items;
            $subtotal  = collect($itemsData)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $discount  = (float) $request->discount ?? 0;
            $tax       = (float) $request->tax ?? 0;
            $grandTotal = $subtotal - $discount + ($subtotal * $tax / 100);

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber !== '' ? $quotationNumber : Quotation::generateNumber(),
                'customer_id'      => $request->customer_id,
                'date'             => $request->date,
                'valid_until'      => $request->valid_until,
                'subtotal'         => $subtotal,
                'discount'         => $discount,
                'tax'              => $tax,
                'grand_total'      => $grandTotal,
                'notes'            => $request->notes,
                'terms'            => $request->terms,
                'status'           => 'draft',
                'prepared_by'      => $request->prepared_by,
                'created_by'       => auth()->id(),
            ]);

            foreach ($itemsData as $index => $itemData) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_name'    => $itemData['item_name'],
                    'description'  => $itemData['description'] ?? null,
                    'size'         => $itemData['size'] ?? null,
                    'quantity'     => $itemData['quantity'],
                    'unit'         => $itemData['unit'] ?? 'Unit',
                    'unit_price'   => $itemData['unit_price'],
                    'total'        => $itemData['quantity'] * $itemData['unit_price'],
                    'sort_order'   => $index,
                ]);
            }
        });

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation created successfully.');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $quotation->load('items');
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $items     = Item::where('is_active', true)->orderBy('name')->get();

        return view('quotations.edit', compact('quotation', 'customers', 'items'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'customer_id'           => 'required|exists:customers,id',
            'date'                  => 'required|date',
            'prepared_by'           => 'nullable|string|max:255',
            'items'                 => 'required|array|min:1',
            'items.*.item_name'     => 'required|string',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            $itemsData  = $request->items;
            $subtotal   = collect($itemsData)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $discount   = (float) $request->discount ?? 0;
            $tax        = (float) $request->tax ?? 0;
            $grandTotal = $subtotal - $discount + ($subtotal * $tax / 100);

            $quotation->update([
                'customer_id' => $request->customer_id,
                'date'        => $request->date,
                'valid_until' => $request->valid_until,
                'subtotal'    => $subtotal,
                'discount'    => $discount,
                'tax'         => $tax,
                'grand_total' => $grandTotal,
                'notes'       => $request->notes,
                'terms'       => $request->terms,
                'status'      => $request->status ?? $quotation->status,
                'prepared_by' => $request->prepared_by,
            ]);

            $quotation->items()->delete();

            foreach ($itemsData as $index => $itemData) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_name'    => $itemData['item_name'],
                    'description'  => $itemData['description'] ?? null,
                    'size'         => $itemData['size'] ?? null,
                    'quantity'     => $itemData['quantity'],
                    'unit'         => $itemData['unit'] ?? 'Unit',
                    'unit_price'   => $itemData['unit_price'],
                    'total'        => $itemData['quantity'] * $itemData['unit_price'],
                    'sort_order'   => $index,
                ]);
            }
        });

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        try {
            $quotation->delete();
            return redirect()->route('quotations.index')
                ->with('success', 'Quotation and all items deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('quotations.index')
                ->with('error', 'Failed to delete quotation: ' . $e->getMessage());
        }
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        $company = config('company');

        // Load DB-overridden company settings and logo
        $dbSettings = ['name','owner','address','phone','email','tagline','currency','currency_symbol'];
        foreach ($dbSettings as $key) {
            $val = \App\Models\Setting::get("company_{$key}") ?? \App\Models\Setting::get($key);
            if ($val) $company[$key] = $val;
        }
        $logoPath = \App\Models\Setting::get('company_logo');
        $company['logo_base64'] = null;
        if (extension_loaded('gd') && $logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath)) {
            $file = \Illuminate\Support\Facades\Storage::disk('public')->get($logoPath);
            $mime = mime_content_type(\Illuminate\Support\Facades\Storage::disk('public')->path($logoPath));
            $company['logo_base64'] = 'data:' . $mime . ';base64,' . base64_encode($file);
        }

        $pdf = Pdf::loadView('pdf.quotation', compact('quotation', 'company'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($quotation->quotation_number . '.pdf');
    }

    public function duplicate(Quotation $quotation)
    {
        $quotation->load('items');

        DB::transaction(function () use ($quotation) {
            $new = $quotation->replicate();
            $new->quotation_number = Quotation::generateNumber();
            $new->status = 'draft';
            $new->date   = now()->toDateString();
            $new->created_by = auth()->id();
            $new->save();

            foreach ($quotation->items as $item) {
                $newItem = $item->replicate();
                $newItem->quotation_id = $new->id;
                $newItem->save();
            }
        });

        return redirect()->route('quotations.index')
            ->with('success', 'Quotation duplicated successfully.');
    }

    public function convertToOrder(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);

        DB::transaction(function () use ($quotation) {
            $order = Order::create([
                'order_number'  => Order::generateNumber(),
                'customer_id'   => $quotation->customer_id,
                'quotation_id'  => $quotation->id,
                'order_date'    => now()->toDateString(),
                'subtotal'      => $quotation->subtotal,
                'discount'      => $quotation->discount,
                'tax'           => $quotation->tax,
                'grand_total'   => $quotation->grand_total,
                'status'        => 'pending',
                'notes'         => $quotation->notes,
                'created_by'    => auth()->id(),
            ]);

            foreach ($quotation->items as $item) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'item_name'   => $item->item_name,
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit'        => $item->unit,
                    'unit_price'  => $item->unit_price,
                    'total'       => $item->total,
                    'sort_order'  => $item->sort_order,
                ]);
            }

            $quotation->update(['status' => 'converted']);
        });

        return redirect()->route('orders.index')
            ->with('success', 'Quotation converted to order successfully.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate(['status' => 'required|in:draft,sent,accepted,rejected,converted']);
        $quotation->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }
}
