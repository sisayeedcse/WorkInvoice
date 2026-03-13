<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $years = range(now()->year, now()->year - 4);

        return view('reports.index', compact(
            'monthlyRevenue', 'monthlyLabels', 'monthlyOrders', 'monthlyQuotations',
            'topCustomers', 'orderStatusSummary', 'quotationStatusSummary',
            'totalRevenue', 'totalOrders', 'totalQuotations', 'completedOrders',
            'year', 'years'
        ));
    }
}
