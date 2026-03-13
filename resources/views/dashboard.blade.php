@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Welcome back, {{ auth()->user()->name }}. Here's your
                business overview.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('quotations.create') }}" class="btn btn-accent">
                <i class="bi bi-plus-lg me-1"></i> New Quotation
            </a>
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> New Order
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

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-graph-up me-2 text-accent" style="color:var(--accent);"></i>Monthly Revenue</span>
                    <span class="badge bg-light text-muted">Last 6 Months</span>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Orders / Month</span>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="190"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tables -->
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
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
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
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

        // Revenue chart
        new Chart(document.getElementById('revenueChart'), {
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
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: v => 'AED ' + v.toLocaleString() } }
                }
            }
        });

        // Orders chart
        new Chart(document.getElementById('ordersChart'), {
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
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 } }
                }
            }
        });
    </script>
@endpush