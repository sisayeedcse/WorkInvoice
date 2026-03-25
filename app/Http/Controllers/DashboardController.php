<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Sale;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalQuotations = Quotation::count();
        $totalOrders     = Order::count();
        $totalRevenue    = Invoice::where('status', 'paid')->sum('grand_total');
        $pendingJobs     = Order::whereIn('status', ['pending', 'approved', 'in_progress'])->count();
        $completedJobs   = Order::whereIn('status', ['completed', 'delivered'])->count();

        // Project statistics
        $allProjects = Project::all();
        $totalProjects = $allProjects->count();
        $activeProjects = Project::whereIn('status', ['started', 'in_progress'])->count();
        $completedProjects = Project::whereIn('status', ['completed', 'delivered'])->count();
        $totalProfit = $allProjects->sum('net_profit');
        $projectsDue = Project::where('payment_status', '!=', 'paid')
                             ->whereIn('status', ['completed', 'delivered'])
                             ->count();

        // Monthly revenue for the past 6 months
        $monthlyRevenue = [];
        $monthlyLabels  = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthlyLabels[] = $month->format('M Y');
            $monthlyRevenue[] = Payment::whereYear('payment_date', $month->year)
                ->whereMonth('payment_date', $month->month)
                ->sum('amount');
        }

        // Orders per month for the past 6 months
        $ordersPerMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $ordersPerMonth[] = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        $recentQuotations = Quotation::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $recentOrders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        // POS + inventory summary
        $todaySales = Sale::whereDate('sale_date', today())->with('items')->get();
        $todaySalesAmount = $todaySales->sum('grand_total');
        $todayItemsSold = $todaySales->sum(function ($sale) {
            return $sale->items->sum('quantity');
        });
        $todaySalesCount = $todaySales->count();

        $lowStockItems = Item::where('track_inventory', true)
            ->whereNotNull('reorder_level')
            ->whereColumn('stock_quantity', '<', 'reorder_level')
            ->orderBy('stock_quantity')
            ->take(8)
            ->get();

        // DAILY BUSINESS SUMMARY - Total Retail + Project Income
        $todayProjectIncome = Project::whereDate('created_at', today())
            ->sum('total_revenue'); // Could also use updated_at for today's changes
        $todayProjectExpenses = DB::table('project_expenses')
            ->whereDate('expense_date', today())
            ->sum('amount');
        $todayNetProfit = ($todaySalesAmount + $todayProjectIncome) - $todayProjectExpenses;

        return view('dashboard', compact(
            'totalQuotations', 'totalOrders', 'totalRevenue',
            'pendingJobs', 'completedJobs',
            'totalProjects', 'activeProjects', 'completedProjects', 'totalProfit', 'projectsDue',
            'monthlyRevenue', 'monthlyLabels', 'ordersPerMonth',
            'recentQuotations', 'recentOrders',
            'todaySalesAmount', 'todayItemsSold', 'todaySalesCount', 'lowStockItems',
            'todayProjectIncome', 'todayProjectExpenses', 'todayNetProfit'
        ));
    }
}
