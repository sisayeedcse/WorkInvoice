<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->get();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $terms     = config('company.terms');
        return view('invoices.create', compact('customers', 'terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'           => 'required|exists:customers,id',
            'date'                  => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.item_name'     => 'required|string',
            'items.*.quantity'      => 'required|numeric|min:0.01',
            'items.*.unit_price'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $itemsData  = $request->items;
            $subtotal   = collect($itemsData)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $discount   = (float) ($request->discount ?? 0);
            $tax        = (float) ($request->tax ?? 0);
            $grandTotal = $subtotal - $discount + ($subtotal * $tax / 100);

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'customer_id'    => $request->customer_id,
                'date'           => $request->date,
                'due_date'       => $request->due_date,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'grand_total'    => $grandTotal,
                'paid_amount'    => 0,
                'balance'        => $grandTotal,
                'status'         => 'draft',
                'notes'          => $request->notes,
                'terms'          => $request->terms,
                'created_by'     => auth()->id(),
            ]);

            foreach ($itemsData as $index => $itemData) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'item_name'   => $itemData['item_name'],
                    'description' => $itemData['description'] ?? null,
                    'quantity'    => $itemData['quantity'],
                    'unit'        => $itemData['unit'] ?? 'Unit',
                    'unit_price'  => $itemData['unit_price'],
                    'total'       => $itemData['quantity'] * $itemData['unit_price'],
                    'sort_order'  => $index,
                ]);
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments', 'order']);
        return view('invoices.show', compact('invoice'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
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

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'company'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount'       => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_date' => 'required|date',
            'method'       => 'required|in:cash,bank_transfer,online,cheque,other',
            'reference'    => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            Payment::create([
                'invoice_id'   => $invoice->id,
                'customer_id'  => $invoice->customer_id,
                'payment_date' => $request->payment_date,
                'amount'       => $request->amount,
                'method'       => $request->method,
                'reference'    => $request->reference,
                'notes'        => $request->notes,
                'created_by'   => auth()->id(),
            ]);

            $newPaid    = $invoice->paid_amount + $request->amount;
            $newBalance = $invoice->grand_total - $newPaid;
            $newStatus  = $newBalance <= 0 ? 'paid' : 'partial';

            $invoice->update([
                'paid_amount' => $newPaid,
                'balance'     => max(0, $newBalance),
                'status'      => $newStatus,
            ]);
        });

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,sent,partial,paid,overdue,cancelled']);
        $invoice->update(['status' => $request->status]);
        return back()->with('success', 'Status updated.');
    }
}
