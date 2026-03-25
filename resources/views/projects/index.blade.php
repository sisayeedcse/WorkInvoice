@extends('layouts.app')
@section('title', 'Projects')
@section('page-title', 'Projects')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-kanban"></i> Project Management</div>
            <h1 class="hero-title">Projects</h1>
            <p class="hero-copy">Track real-world execution, expenses, and profitability for each workshop job.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-navy">
                <div class="stat-icon"><i class="bi bi-kanban-fill"></i></div>
                <div class="stat-value">{{ number_format($totalProjects) }}</div>
                <div class="stat-label">Total Projects</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-orange">
                <div class="stat-icon"><i class="bi bi-play-circle-fill"></i></div>
                <div class="stat-value">{{ number_format($activeProjects) }}</div>
                <div class="stat-label">Active Projects</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card {{ $totalProfit >= 0 ? 'bg-green' : 'bg-red' }}">
                <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-value">{{ number_format($totalProfit, 0) }}</div>
                <div class="stat-label">Total Profit (AED)</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card bg-amber">
                <div class="stat-icon"><i class="bi bi-exclamation-circle-fill"></i></div>
                <div class="stat-value">{{ number_format($totalDue, 0) }}</div>
                <div class="stat-label">Total Due (AED)</div>
            </div>
        </div>
    </div>

    <!-- Filters & Table -->
    <div class="table-card">
        <div class="table-card-header">
            <span><i class="bi bi-kanban me-2" style="color:var(--accent);"></i>All Projects</span>
        </div>

        <!-- Filters Toolbar -->
        <div class="card-body border-bottom">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>Started</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Payment Status</label>
                    <select name="payment_status" class="form-select form-select-sm">
                        <option value="">All Payment Statuses</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partial
                        </option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Project name or customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Projects Table -->
        <div class="table-responsive">
            <table class="table mb-0" id="projectsTable">
                <thead>
                    <tr class="table-light">
                        <th>Project #</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th class="text-end">Revenue</th>
                        <th class="text-end">Expense</th>
                        <th class="text-end">Net Profit</th>
                        <th>Payment</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td>
                                <a href="{{ route('projects.show', $project) }}" class="doc-number text-decoration-none">
                                    {{ $project->project_number }}
                                </a>
                            </td>
                            <td>{{ $project->order->customer->name ?? '—' }}</td>
                            <td>{!! $project->status_badge !!}</td>
                            <td class="text-end">AED {{ number_format($project->total_revenue, 2) }}</td>
                            <td class="text-end text-danger">AED {{ number_format($project->total_expense, 2) }}</td>
                            <td
                                class="text-end fw-semibold
                                        {{ $project->net_profit > 0 ? 'text-success' : ($project->net_profit < 0 ? 'text-danger' : 'text-muted') }}">
                                AED {{ number_format($project->net_profit, 2) }}
                            </td>
                            <td>
                                {!! $project->payment_status_badge !!}
                                @if(in_array($project->status, ['completed', 'delivered']) && $project->payment_status != 'paid')
                                    <span class="badge bg-danger ms-1">Due</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No projects yet. Create projects from Orders.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        if (document.getElementById('projectsTable')) {
            new DataTable('#projectsTable', {
                pageLength: 25,
                order: [[0, 'desc']],
                language: {
                    search: '',
                    searchPlaceholder: 'Quick search...',
                }
            });
        }
    </script>
@endpush