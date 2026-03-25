<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
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

        return view('dashboard', compact(
            'totalQuotations', 'totalOrders', 'totalRevenue',
            'pendingJobs', 'completedJobs',
            'totalProjects', 'activeProjects', 'completedProjects', 'totalProfit', 'projectsDue',
            'monthlyRevenue', 'monthlyLabels', 'ordersPerMonth',
            'recentQuotations', 'recentOrders'
        ));
    }
}
