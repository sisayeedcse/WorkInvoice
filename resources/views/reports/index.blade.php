@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div class="page-header">
    <div>
        <h1>Reports & Analytics</h1>
        <p class="text-muted mb-0" style="font-size:13px;">Business performance overview</p>
    </div>
    <form method="GET" class="d-flex align-items-center gap-2">
        <label class="form-label mb-0 text-muted">Year:</label>
        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="width:110px;">
            @foreach($years as $y)
            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-green">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-value">{{ number_format($totalRevenue, 0) }}</div>
            <div class="stat-label">Revenue {{ $year }} (AED)</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-navy">
            <div class="stat-icon"><i class="bi bi-clipboard-check-fill"></i></div>
            <div class="stat-value">{{ $totalOrders }}</div>
            <div class="stat-label">Orders {{ $year }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-orange">
            <div class="stat-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
            <div class="stat-value">{{ $totalQuotations }}</div>
            <div class="stat-label">Quotations {{ $year }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-blue">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-value">{{ $completedOrders }}</div>
            <div class="stat-label">Completed {{ $year }}</div>
        </div>
    </div>
</div>

<!-- Charts Row 1 -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-graph-up me-2" style="color:var(--accent);"></i>Monthly Revenue — {{ $year }}</div>
            <div class="card-body">
                <canvas id="revenueChart" height="130"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart me-2" style="color:var(--accent);"></i>Order Status</div>
            <div class="card-body d-flex align-items-center">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row 2 -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Orders Per Month — {{ $year }}</div>
            <div class="card-body">
                <canvas id="ordersChart" height="140"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Quotations Per Month — {{ $year }}</div>
            <div class="card-body">
                <canvas id="quotationsChart" height="140"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top Customers -->
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-trophy me-2" style="color:var(--accent);"></i>Top Customers by Revenue</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>#</th><th>Customer</th><th class="text-end">Total Paid (AED)</th></tr></thead>
                    <tbody>
                    @forelse($topCustomers as $i => $customer)
                    <tr>
                        <td>
                            <span class="badge {{ $i == 0 ? 'bg-warning text-dark' : ($i == 1 ? 'bg-secondary' : 'bg-light text-dark') }}">{{ $i + 1 }}</span>
                        </td>
                        <td>
                            <a href="{{ route('customers.show', $customer) }}" class="fw-semibold text-decoration-none" style="color:var(--primary);">{{ $customer->name }}</a>
                        </td>
                        <td class="text-end fw-semibold" style="color:var(--accent);">{{ number_format($customer->total_paid ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">No data yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-file-earmark-check me-2" style="color:var(--accent);"></i>Quotation Status Summary</div>
            <div class="card-body">
                @foreach(['draft' => 'Secondary', 'sent' => 'Info', 'accepted' => 'Success', 'rejected' => 'Danger', 'converted' => 'Primary'] as $status => $color)
                @php $count = $quotationStatusSummary[$status] ?? 0; $total = array_sum($quotationStatusSummary->toArray() ?: [1]); $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:90px;font-size:12.5px;" class="text-muted">{{ ucfirst($status) }}</div>
                    <div class="flex-grow-1">
                        <div class="progress" style="height:8px;border-radius:4px;">
                            <div class="progress-bar bg-{{ strtolower($color) }}" style="width:{{ $pct }}%;"></div>
                        </div>
                    </div>
                    <div style="width:40px;text-align:right;font-size:12.5px;font-weight:600;">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const labels = @json($monthlyLabels);

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Revenue (AED)', data: @json($monthlyRevenue),
            backgroundColor: 'rgba(244,124,28,.85)', borderRadius: 6, barThickness: 24,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false }, ticks: { font: { size: 10 } } }, y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, callback: v => 'AED '+v.toLocaleString() } } } }
});

new Chart(document.getElementById('ordersChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{ label: 'Orders', data: @json($monthlyOrders), backgroundColor: 'rgba(26,43,74,.75)', borderRadius: 5, barThickness: 24 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false }, ticks: { font: { size: 10 } } }, y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, stepSize: 1 } } } }
});

new Chart(document.getElementById('quotationsChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{ label: 'Quotations', data: @json($monthlyQuotations), borderColor: '#f47c1c', backgroundColor: 'rgba(244,124,28,.08)', fill: true, tension: .4, borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#f47c1c' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } },
        scales: { x: { grid: { display: false }, ticks: { font: { size: 10 } } }, y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, stepSize: 1 } } } }
});

// Order status donut
const statusData = @json($orderStatusSummary);
const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_',' '));
const statusValues = Object.values(statusData);
new Chart(document.getElementById('orderStatusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusValues.length ? statusValues : [1],
            backgroundColor: ['#f59e0b','#3b82f6','#f47c1c','#22c55e','#16a34a','#ef4444'],
            borderWidth: 0 }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } } }
});
</script>
@endpush
