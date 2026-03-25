<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::whereNull('deleted_at')->with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('orders.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'           => 'required|exists:customers,id',
            'order_date'            => 'required|date',
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

            $order = Order::create([
                'order_number'  => Order::generateNumber(),
                'customer_id'   => $request->customer_id,
                'order_date'    => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'subtotal'      => $subtotal,
                'discount'      => $discount,
                'tax'           => $tax,
                'grand_total'   => $grandTotal,
                'status'        => 'pending',
                'notes'         => $request->notes,
                'delivery_info' => $request->delivery_info,
                'created_by'    => auth()->id(),
            ]);

            foreach ($itemsData as $index => $itemData) {
                OrderItem::create([
                    'order_id'    => $order->id,
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

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items', 'quotation', 'invoice']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load('items');
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'order_date'   => 'required|date',
            'status'       => 'required|in:pending,approved,in_progress,completed,delivered,cancelled',
        ]);

        $order->update([
            'customer_id'   => $request->customer_id,
            'order_date'    => $request->order_date,
            'delivery_date' => $request->delivery_date,
            'status'        => $request->status,
            'notes'         => $request->notes,
            'delivery_info' => $request->delivery_info,
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,approved,in_progress,completed,delivered,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Status updated to ' . ucfirst(str_replace('_', ' ', $request->status)) . '.');
    }

    public function destroy(Order $order)
    {
        try {
            $order->delete();
            return redirect()->route('orders.index')->with('success', 'Order and all items deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with('error', 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function convertToInvoice(Order $order)
    {
        $order->load(['customer', 'items']);

        if ($order->invoice) {
            return redirect()->route('invoices.show', $order->invoice)
                ->with('info', 'Invoice already exists for this order.');
        }

        DB::transaction(function () use ($order) {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'customer_id'    => $order->customer_id,
                'order_id'       => $order->id,
                'date'           => now()->toDateString(),
                'due_date'       => now()->addDays(30)->toDateString(),
                'subtotal'       => $order->subtotal,
                'discount'       => $order->discount,
                'tax'            => $order->tax,
                'grand_total'    => $order->grand_total,
                'paid_amount'    => 0,
                'balance'        => $order->grand_total,
                'status'         => 'draft',
                'created_by'     => auth()->id(),
            ]);

            foreach ($order->items as $item) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'item_name'   => $item->item_name,
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit'        => $item->unit,
                    'unit_price'  => $item->unit_price,
                    'total'       => $item->total,
                    'sort_order'  => $item->sort_order,
                ]);
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice created from order.');
    }

    public function convertToProject(Order $order)
    {
        $order->load('customer');

        // Check if project already exists
        if ($order->project) {
            return redirect()->route('projects.show', $order->project)
                ->with('info', 'Project already exists for this order.');
        }

        $project = null;

        DB::transaction(function () use ($order, &$project) {
            $project = Project::create([
                'project_number'   => Project::generateNumber(),
                'order_id'         => $order->id,
                'project_name'     => $order->customer->name . ' - ' . $order->order_number,
                'start_date'       => now()->toDateString(),
                'status'           => 'pending',
                'total_revenue'    => $order->grand_total,
                'advance_received' => 0,
                'payment_status'   => 'unpaid',
                'created_by'       => auth()->id(),
            ]);
        });

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }
}
