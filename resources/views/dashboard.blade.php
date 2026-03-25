@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-grid-1x2-fill"></i> Operations Overview</div>
            <h1 class="hero-title">Dashboard</h1>
            <p class="hero-copy">Welcome back, {{ auth()->user()->name }}. Review revenue, operational activity, and recent
                commercial documents from one clean workspace.</p>
            <div class="highlight-strip mt-3">
                <span class="highlight-chip"><i class="bi bi-cash-stack"></i>AED {{ number_format($totalRevenue, 0) }}
                    revenue</span>
                <span class="highlight-chip"><i class="bi bi-hourglass-split"></i>{{ number_format($pendingJobs) }} active
                    jobs</span>
                <span class="highlight-chip"><i class="bi bi-check-circle"></i>{{ number_format($completedJobs) }}
                    completed</span>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('quotations.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg"></i> New Quotation
            </a>
            <a href="{{ route('orders.create') }}" class="btn btn-light">
                <i class="bi bi-plus-lg"></i> New Order
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-navy">
                <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                <div class="stat-value">{{ number_format($totalQuotations) }}</div>
                <div class="stat-label">Total Quotations</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-orange">
                <div class="stat-icon"><i class="bi bi-clipboard-check-fill"></i></div>
                <div class="stat-value">{{ number_format($totalOrders) }}</div>
                <div class="stat-label">Total Orders</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-green">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value">{{ number_format($totalRevenue, 0) }}</div>
                <div class="stat-label">Revenue (AED)</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-blue">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value">{{ number_format($pendingJobs) }}</div>
                <div class="stat-label">Pending Jobs</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-amber">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-value">{{ number_format($completedJobs) }}</div>
                <div class="stat-label">Completed Jobs</div>
            </div>
        </div>
    </div>

    <!-- Project Statistics Row -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="text-muted mb-3"><i class="bi bi-kanban me-2"></i>Project Performance</h6>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="stat-icon"><i class="bi bi-kanban-fill"></i></div>
                <div class="stat-value">{{ number_format($totalProjects) }}</div>
                <div class="stat-label">Total Projects</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-blue">
                <div class="stat-icon"><i class="bi bi-play-circle-fill"></i></div>
                <div class="stat-value">{{ number_format($activeProjects) }}</div>
                <div class="stat-label">Active Projects</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-green">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-value">{{ number_format($completedProjects) }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card {{ $totalProfit >= 0 ? 'bg-green' : 'bg-red' }}">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-value">{{ $totalProfit >= 0 ? '' : '-' }}{{ number_format(abs($totalProfit), 0) }}</div>
                <div class="stat-label">Total Profit (AED)</div>
            </div>
        </div>
        <div class="col-6 col-lg-2-4">
            <div class="stat-card bg-amber">
                <div class="stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
                <div class="stat-value">{{ number_format($projectsDue) }}</div>
                <div class="stat-label">Payment Due</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="text-muted mb-3"><i class="bi bi-shop me-2"></i>Retail & Inventory Today</h6>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card bg-green">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-value">{{ number_format($todaySalesAmount, 2) }}</div>
                <div class="stat-label">POS Revenue Today (AED)</div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card bg-orange">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-value">{{ number_format($todaySalesCount) }}</div>
                <div class="stat-label">Sales Today</div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card bg-amber">
                <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
                <div class="stat-value">{{ number_format($todayItemsSold, 3) }}</div>
                <div class="stat-label">Items Sold Today</div>
            </div>
        </div>
    </div>

    <!-- DAILY BUSINESS SUMMARY -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="text-muted mb-3"><i class="bi bi-graph-up me-2"></i>Daily Business Summary</h6>
        </div>
        <div class="col-12 col-lg-3">
            <div class="stat-card bg-blue">
                <div class="stat-icon"><i class="bi bi-shop-window"></i></div>
                <div class="stat-value">{{ number_format($todaySalesAmount, 2) }}</div>
                <div class="stat-label">Retail Sales Today</div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="stat-card bg-purple">
                <div class="stat-icon"><i class="bi bi-briefcase"></i></div>
                <div class="stat-value">{{ number_format($todayProjectIncome, 2) }}</div>
                <div class="stat-label">Project Income Today</div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="stat-card bg-red">
                <div class="stat-icon"><i class="bi bi-dash-circle"></i></div>
                <div class="stat-value">{{ number_format($todayProjectExpenses, 2) }}</div>
                <div class="stat-label">Total Expenses Today</div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="stat-card {{ $todayNetProfit >= 0 ? 'bg-green' : 'bg-red' }}">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-value">{{ $todayNetProfit >= 0 ? '' : '-' }}{{ number_format(abs($todayNetProfit), 2) }}
                </div>
                <div class="stat-label">Net Profit Today</div>
            </div>
        </div>
    </div>

    <div class="table-card mb-4">
        <div class="table-card-header">
            <span><i class="bi bi-exclamation-triangle me-2" style="color:var(--accent);"></i>Low Stock Alerts</span>
            <a href="{{ route('products.index', ['stock_status' => 'low']) }}"
                class="btn btn-sm btn-outline-secondary rounded-pill">View Products</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lowStockItems as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ number_format($item->stock_quantity, 3) }} {{ $item->unit }}</td>
                            <td>{{ number_format($item->reorder_level, 3) }} {{ $item->unit }}</td>
                            <td>{!! $item->low_stock_badge !!}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No low stock alerts.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="table-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-graph-up me-2 text-accent" style="color:var(--accent);"></i>Monthly Revenue</span>
                    <span class="badge badge-secondary">Last 6 Months</span>
                </div>
                <div class="card-body" style="height:300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="table-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Orders / Month</span>
                </div>
                <div class="card-body" style="height:300px;">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tables -->
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="table-card-header">
                    <span><i class="bi bi-file-earmark-text me-2" style="color:var(--accent);"></i>Recent Quotations</span>
                    <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View
                        All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentQuotations as $q)
                                <tr>
                                    <td><a href="{{ route('quotations.show', $q) }}"
                                            class="doc-number text-decoration-none">{{ $q->quotation_number }}</a></td>
                                    <td>{{ $q->customer->name ?? '—' }}</td>
                                    <td>AED {{ number_format($q->grand_total, 2) }}</td>
                                    <td>{!! $q->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No quotations yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="table-card-header">
                    <span><i class="bi bi-clipboard-check me-2" style="color:var(--accent);"></i>Recent Orders</span>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $o)
                                <tr>
                                    <td><a href="{{ route('orders.show', $o) }}"
                                            class="doc-number text-decoration-none">{{ $o->order_number }}</a></td>
                                    <td>{{ $o->customer->name ?? '—' }}</td>
                                    <td>AED {{ number_format($o->grand_total, 2) }}</td>
                                    <td>{!! $o->status_badge !!}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($monthlyLabels);
        const revenue = @json($monthlyRevenue);
        const orders = @json($ordersPerMonth);
        const commonChartOptions = { responsive: true, maintainAspectRatio: false, animation: false };

        function createChart(canvasId, config) {
            const canvas = document.getElementById(canvasId);
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();
            return new Chart(canvas, config);
        }

        // Revenue chart
        createChart('revenueChart', {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (AED)',
                    data: revenue,
                    backgroundColor: 'rgba(244,124,28,.85)',
                    borderRadius: 6,
                    barThickness: 28,
                }]
            },
            options: {
                ...commonChartOptions,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: v => 'AED ' + v.toLocaleString() } }
                }
            }
        });

        // Orders chart
        createChart('ordersChart', {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Orders',
                    data: orders,
                    borderColor: '#1a2b4a',
                    backgroundColor: 'rgba(26,43,74,.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: .4,
                    pointBackgroundColor: '#1a2b4a',
                    pointRadius: 4,
                }]
            },
            options: {
                ...commonChartOptions,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } }
                }
            }
        });
    </script>
@endpush