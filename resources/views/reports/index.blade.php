@extends('layouts.app')
@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-bar-chart-line-fill"></i> Performance Reporting</div>
            <h1 class="hero-title">Reports & Analytics</h1>
            <p class="hero-copy">Review commercial output, order completion, and customer contribution with executive-level clarity.</p>
        </div>
        <div class="hero-actions">
            <form method="GET" class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 text-white">Year:</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="width:110px;">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
                </select>
            </form>
        </div>
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
            <div class="table-card">
                <div class="card-header"><i class="bi bi-graph-up me-2" style="color:var(--accent);"></i>Monthly Revenue —
                    {{ $year }}</div>
                <div class="card-body" style="height:300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="table-card h-100">
                <div class="card-header"><i class="bi bi-pie-chart me-2" style="color:var(--accent);"></i>Order Status</div>
                <div class="card-body d-flex align-items-center justify-content-center" style="height:300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Orders Per Month —
                    {{ $year }}</div>
                <div class="card-body" style="height:320px;">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-bar-chart me-2" style="color:var(--accent);"></i>Quotations Per
                    Month — {{ $year }}</div>
                <div class="card-body" style="height:320px;">
                    <canvas id="quotationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-trophy me-2" style="color:var(--accent);"></i>Top Customers by
                    Revenue</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer</th>
                                <th class="text-end">Total Paid (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $i => $customer)
                                <tr>
                                    <td>
                                        <span
                                            class="badge {{ $i == 0 ? 'bg-warning text-dark' : ($i == 1 ? 'bg-secondary' : 'bg-light text-dark') }}">{{ $i + 1 }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('customers.show', $customer) }}"
                                            class="fw-semibold text-decoration-none"
                                            style="color:var(--primary);">{{ $customer->name }}</a>
                                    </td>
                                    <td class="text-end fw-semibold" style="color:var(--accent);">
                                        {{ number_format($customer->total_paid ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">No data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-file-earmark-check me-2" style="color:var(--accent);"></i>Quotation
                    Status Summary</div>
                <div class="card-body">
                    @foreach(['draft' => 'Secondary', 'sent' => 'Info', 'accepted' => 'Success', 'rejected' => 'Danger', 'converted' => 'Primary'] as $status => $color)
                        @php $count = $quotationStatusSummary[$status] ?? 0;
                            $total = array_sum($quotationStatusSummary->toArray() ?: [1]);
                        $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
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

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-calendar-day me-2" style="color:var(--accent);"></i>Daily Sales Report</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Sales</th>
                                <th class="text-end">Revenue (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailySalesReport->sortByDesc('sale_date')->take(15) as $daily)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($daily->sale_date)->format('d M Y') }}</td>
                                    <td class="text-center">{{ $daily->sales_count }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($daily->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No sales data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-fire me-2" style="color:var(--accent);"></i>Top Selling Products</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Qty Sold</th>
                                <th class="text-end">Amount (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSellingProducts as $product)
                                <tr>
                                    <td>{{ $product->item_name }}</td>
                                    <td class="text-center">{{ number_format($product->total_quantity, 3) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($product->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No sales data yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-8">
            <div class="table-card">
                <div class="card-header"><i class="bi bi-box-seam me-2" style="color:var(--accent);"></i>Stock Report</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th class="text-end">Current Stock</th>
                                <th class="text-end">Minimum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockReport as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $item->item_type)) }}</td>
                                    <td class="text-end">{{ number_format($item->stock_quantity, 3) }} {{ $item->unit }}</td>
                                    <td class="text-end">{{ $item->reorder_level !== null ? number_format($item->reorder_level, 3) . ' ' . $item->unit : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No stock-tracked items yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="table-card h-100">
                <div class="card-header"><i class="bi bi-exclamation-triangle me-2" style="color:var(--accent);"></i>Low Stock Report</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockReport as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-end text-danger fw-semibold">{{ number_format($item->stock_quantity, 3) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">No low stock items.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: RETAIL SALES REPORT -->
    <div class="row g-3 mt-4">
        <div class="col-12">
            <h6 class="text-muted mb-3"><i class="bi bi-shop me-2"></i>Retail Sales Report — {{ $year }}</h6>
        </div>
        <div class="col-12">
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-center">Sales Count</th>
                                <th class="text-end">Total Revenue (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dailySalesReport as $report)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->sale_date)->format('M d, Y') }}</td>
                                    <td class="text-center">{{ $report->sales_count }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($report->total_revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No retail sales data for {{ $year }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: PROJECT REVENUE REPORT -->
    <div class="row g-3 mt-4">
        <div class="col-12">
            <h6 class="text-muted mb-3"><i class="bi bi-briefcase me-2"></i>Project Revenue Report — {{ $year }}</h6>
        </div>
        <div class="col-12 col-lg-8">
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Project Date</th>
                                <th class="text-center">Projects</th>
                                <th class="text-end">Total Income (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projectRevenueReport as $report)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->project_date)->format('M d, Y') }}</td>
                                    <td class="text-center">{{ $report->project_count }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($report->total_income, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No project data for {{ $year }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="table-card h-100">
                <div class="card-header"><i class="bi bi-percent me-2" style="color:var(--accent);"></i>Project Expenses by Category</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Amount (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projectExpensesReport as $expense)
                                <tr>
                                    <td><span class="badge {{ match($expense->category) { 'materials' => 'bg-navy', 'labor' => 'bg-info', 'transport' => 'bg-secondary', 'tools' => 'bg-dark', default => 'bg-secondary' } }}">{{ ucfirst(str_replace('_', ' ', $expense->category)) }}</span></td>
                                    <td class="text-end fw-semibold">{{ number_format($expense->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">No expense data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: MANUFACTURING COST REPORT -->
    <div class="row g-3 mt-4">
        <div class="col-12">
            <h6 class="text-muted mb-3"><i class="bi bi-gear-wide-connected me-2"></i>Manufacturing Cost Report — {{ $year }}</h6>
        </div>
        <div class="col-12 col-lg-8">
            <div class="table-card">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Production #</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Total Material Cost (AED)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($manufacturingCostReport as $prod)
                                <tr>
                                    <td><code>{{ $prod->production_number }}</code></td>
                                    <td class="text-center">{{ number_format($prod->quantity_to_produce, 3) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($prod->total_material_cost ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No manufacturing data for {{ $year }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="table-card h-100">
                <div class="card-header"><i class="bi bi-exclamation-triangle me-2" style="color:var(--accent);"></i>Low Raw Materials</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th class="text-end">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowMaterials as $item)
                                <tr>
                                    <td><small>{{ $item->name }}</small></td>
                                    <td class="text-end text-danger fw-semibold"><small>{{ number_format($item->stock_quantity, 3) }}</small></td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted py-3"><small>No low materials.</small></td></tr>
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
        const commonChartOptions = { responsive: true, maintainAspectRatio: false, animation: false };

        function createChart(canvasId, config) {
            const canvas = document.getElementById(canvasId);
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();
            return new Chart(canvas, config);
        }

        createChart('revenueChart', {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Revenue (AED)', data: @json($monthlyRevenue),
                    backgroundColor: 'rgba(244,124,28,.85)', borderRadius: 6, barThickness: 24,
                }]
            },
            options: {
                ...commonChartOptions, plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false }, ticks: { font: { size: 10 } } }, y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, callback: v => 'AED ' + v.toLocaleString() } } }
            }
        });

        createChart('ordersChart', {
            type: 'bar',
            data: {
                labels,
                datasets: [{ label: 'Orders', data: @json($monthlyOrders), backgroundColor: 'rgba(26,43,74,.75)', borderRadius: 5, barThickness: 24 }]
            },
            options: {
                ...commonChartOptions, plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false }, ticks: { font: { size: 10 } } }, y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, stepSize: 1 } } }
            }
        });

        createChart('quotationsChart', {
            type: 'line',
            data: {
                labels,
                datasets: [{ label: 'Quotations', data: @json($monthlyQuotations), borderColor: '#f47c1c', backgroundColor: 'rgba(244,124,28,.08)', fill: true, tension: .4, borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#f47c1c' }]
            },
            options: {
                ...commonChartOptions, plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false }, ticks: { font: { size: 10 } } }, y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, stepSize: 1 } } }
            }
        });

        // Order status donut
        const statusData = @json($orderStatusSummary);
        const statusLabels = Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1).replace('_', ' '));
        const statusValues = Object.values(statusData);
        createChart('orderStatusChart', {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues.length ? statusValues : [1],
                    backgroundColor: ['#f59e0b', '#3b82f6', '#f47c1c', '#22c55e', '#16a34a', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: { ...commonChartOptions, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } } } }
        });
    </script>
@endpush