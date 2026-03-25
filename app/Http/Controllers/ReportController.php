<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProductionOrder;
use App\Models\ProductionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $year = request('year', now()->year);

        // Monthly revenue for selected year
        $monthlyRevenue = [];
        $monthlyLabels  = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyLabels[]  = Carbon::create($year, $m, 1)->format('M');
            $monthlyRevenue[] = Payment::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $m)
                ->sum('amount');
        }

        // Monthly orders for selected year
        $monthlyOrders = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyOrders[] = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }

        // Monthly quotations
        $monthlyQuotations = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyQuotations[] = Quotation::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }

        // Top customers
        $topCustomers = Customer::withSum(['payments as total_paid'], 'amount')
            ->orderByDesc('total_paid')
            ->take(5)
            ->get();

        // Order status summary
        $orderStatusSummary = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Quotation status summary
        $quotationStatusSummary = Quotation::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $totalRevenue     = Payment::whereYear('payment_date', $year)->sum('amount');
        $totalOrders      = Order::whereYear('created_at', $year)->count();
        $totalQuotations  = Quotation::whereYear('created_at', $year)->count();
        $completedOrders  = Order::whereYear('created_at', $year)
            ->whereIn('status', ['completed', 'delivered'])->count();

        $dailySalesReport = Sale::whereYear('sale_date', $year)
            ->selectRaw('sale_date, COUNT(*) as sales_count, SUM(grand_total) as total_revenue')
            ->groupBy('sale_date')
            ->orderBy('sale_date')
            ->get();

        $topSellingProducts = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereYear('sales.sale_date', $year)
            ->selectRaw('sale_items.item_name, SUM(sale_items.quantity) as total_quantity, SUM(sale_items.total) as total_amount')
            ->groupBy('sale_items.item_name')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        $stockReport = Item::where('track_inventory', true)
            ->orderBy('name')
            ->get();

        $lowStockReport = Item::where('track_inventory', true)
            ->whereNotNull('reorder_level')
            ->whereColumn('stock_quantity', '<', 'reorder_level')
            ->orderBy('stock_quantity')
            ->get();

        // NEW: PROJECT REVENUE REPORT
        $projectRevenueReport = Project::whereYear('created_at', $year)
            ->selectRaw('DATE(created_at) as project_date, COUNT(*) as project_count, SUM(total_revenue) as total_income')
            ->groupBy('project_date')
            ->orderBy('project_date')
            ->get();

        // NEW: PROJECT EXPENSES REPORT
        $projectExpensesReport = ProjectExpense::whereYear('expense_date', $year)
            ->selectRaw('category, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        // NEW: MANUFACTURING COST REPORT
        $manufacturingCostReport = ProductionOrder::whereYear('created_at', $year)
            ->with(['materials'])
            ->selectRaw('production_orders.id, production_orders.production_number, production_orders.finished_item_id, production_orders.quantity_to_produce, SUM(production_items.total_cost) as total_material_cost')
            ->leftJoin('production_items', 'production_orders.id', '=', 'production_items.production_order_id')
            ->groupBy('production_orders.id', 'production_orders.production_number', 'production_orders.finished_item_id', 'production_orders.quantity_to_produce')
            ->orderByDesc('created_at')
            ->get();

        // NEW: LOW MATERIAL ALERTS (for manufacturing)
        $lowMaterials = Item::where('track_inventory', true)
            ->where('item_type', 'raw_material')
            ->whereColumn('stock_quantity', '<', 'reorder_level')
            ->orderBy('stock_quantity')
            ->take(10)
            ->get();

        $years = range(now()->year, now()->year - 4);

        return view('reports.index', compact(
            'monthlyRevenue', 'monthlyLabels', 'monthlyOrders', 'monthlyQuotations',
            'topCustomers', 'orderStatusSummary', 'quotationStatusSummary',
            'totalRevenue', 'totalOrders', 'totalQuotations', 'completedOrders',
            'year', 'years', 'dailySalesReport', 'topSellingProducts', 'stockReport', 'lowStockReport',
            'projectRevenueReport', 'projectExpensesReport', 'manufacturingCostReport', 'lowMaterials'
        ));
    }
}
