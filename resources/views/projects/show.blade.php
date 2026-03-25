@extends('layouts.app')
@section('title', $project->project_number)
@section('page-title', $project->project_number)

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-kanban"></i> Project Record</div>
            <h1 class="hero-title">{{ $project->project_number }}</h1>
            <p class="hero-copy">Monitor project execution, track expenses, and measure real profitability.</p>
        </div>
        <div class="hero-actions">
            @if($project->order)
                <a href="{{ route('orders.show', $project->order) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-clipboard-check me-1"></i> View Order
                </a>
            @endif
            @if($project->order && $project->order->invoice)
                <a href="{{ route('invoices.show', $project->order->invoice) }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-receipt me-1"></i> View Invoice
                </a>
            @endif
        </div>
    </div>

    <div class="row g-3">
        <!-- Left Sidebar -->
        <div class="col-12 col-lg-4">
            <!-- Project Info -->
            <div class="card mb-3 record-sidebar">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="doc-number" style="font-size:17px;">{{ $project->project_number }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ $project->start_date->format('d M Y') }}
                            </div>
                        </div>
                        {!! $project->status_badge !!}
                    </div>

                    <!-- Status Update -->
                    <form method="POST" action="{{ route('projects.update-status', $project) }}" class="d-flex gap-2 mb-3">
                        @csrf @method('PATCH')
                        <select name="status" class="form-select form-select-sm">
                            @foreach(['pending', 'started', 'in_progress', 'completed', 'delivered'] as $s)
                                <option value="{{ $s }}" {{ $project->status == $s ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">Update</button>
                    </form>

                    <div class="meta-list mt-3">
                        <div class="meta-item">
                            <span class="meta-label">Customer</span>
                            <div class="meta-value">{{ $project->order->customer->name ?? '—' }}</div>
                        </div>
                        @if($project->order->customer->phone)
                            <div class="meta-item">
                                <span class="meta-label">Phone</span>
                                <div class="meta-value">
                                    <a
                                        href="tel:{{ $project->order->customer->phone }}">{{ $project->order->customer->phone }}</a>
                                </div>
                            </div>
                        @endif
                        @if($project->order)
                            <div class="meta-item">
                                <span class="meta-label">Order</span>
                                <div class="meta-value">
                                    <a href="{{ route('orders.show', $project->order) }}"
                                        class="doc-number">{{ $project->order->order_number }}</a>
                                </div>
                            </div>
                        @endif
                        @if($project->order && $project->order->invoice)
                            <div class="meta-item">
                                <span class="meta-label">Invoice</span>
                                <div class="meta-value">
                                    <a href="{{ route('invoices.show', $project->order->invoice) }}"
                                        class="doc-number">{{ $project->order->invoice->invoice_number }}</a>
                                </div>
                            </div>
                        @endif
                        @if($project->expected_end_date)
                            <div class="meta-item">
                                <span class="meta-label">Expected Completion</span>
                                <div class="meta-value">{{ $project->expected_end_date->format('d M Y') }}</div>
                            </div>
                        @endif
                        @if($project->actual_end_date)
                            <div class="meta-item">
                                <span class="meta-label">Completed On</span>
                                <div class="meta-value text-success">{{ $project->actual_end_date->format('d M Y') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-calculator me-2" style="color:var(--accent);"></i>Financial Summary
                </div>
                <div class="card-body" style="font-size:13.5px;">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Revenue</span>
                        <span class="fw-semibold">AED {{ number_format($project->total_revenue, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-info">
                        <span>Advance Received</span>
                        <span class="fw-semibold">AED {{ number_format($project->advance_received, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Total Expense</span>
                        <span class="fw-semibold">AED {{ number_format($project->total_expense, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold" style="font-size:15px;">
                        <span>Net Profit</span>
                        <span class="{{ $project->net_profit >= 0 ? 'text-success' : 'text-danger' }}">
                            AED {{ number_format($project->net_profit, 2) }}
                        </span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Amount Due</span>
                        <span class="fw-semibold {{ $project->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                            AED {{ number_format($project->due_amount, 2) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted small">Payment Status</span>
                        {!! $project->payment_status_badge !!}
                    </div>
                </div>
            </div>

            <!-- Update Advance -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-cash me-2" style="color:var(--accent);"></i>Advance Payment
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.update-advance', $project) }}">
                        @csrf @method('PATCH')
                        <div class="mb-2">
                            <label class="form-label">Advance Amount (AED)</label>
                            <input type="number" name="advance_received" class="form-control form-control-sm" step="0.01"
                                min="0" max="{{ $project->total_revenue }}"
                                value="{{ number_format($project->advance_received, 2, '.', '') }}">
                            <small class="text-muted">Maximum: AED {{ number_format($project->total_revenue, 2) }}</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-sm">
                            <i class="bi bi-check-circle me-1"></i> Update Advance
                        </button>
                    </form>
                </div>
            </div>

            <!-- Add Expense Form -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-plus-circle me-2" style="color:var(--accent);"></i>Add Expense
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('projects.add-expense', $project) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Expense Name <span class="text-danger">*</span></label>
                            <input type="text" name="expense_name" class="form-control form-control-sm"
                                placeholder="e.g., Steel Purchase" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select form-select-sm" required>
                                <option value="">Select category...</option>
                                <option value="materials">Materials</option>
                                <option value="labor">Labor</option>
                                <option value="transport">Transport</option>
                                <option value="tools">Tools</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Amount (AED) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control form-control-sm" step="0.01" min="0.01"
                                placeholder="0.00" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control form-control-sm"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2"
                                placeholder="Optional details..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-plus-circle me-1"></i> Add Expense
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Content -->
        <div class="col-12 col-lg-8">
            <!-- Expense List -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="bi bi-wallet2 me-2" style="color:var(--accent);"></i>Project Expenses
                    <span class="badge bg-secondary ms-2">{{ $project->expenses->count() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr class="table-light">
                                <th>Date</th>
                                <th>Expense Name</th>
                                <th>Category</th>
                                <th class="text-end">Amount</th>
                                <th>Notes</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($project->expenses as $expense)
                                <tr>
                                    <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                    <td class="fw-medium">{{ $expense->expense_name }}</td>
                                    <td>{!! $expense->category_badge !!}</td>
                                    <td class="text-end text-danger fw-semibold">
                                        AED {{ number_format($expense->amount, 2) }}
                                    </td>
                                    <td class="text-muted">{{ $expense->notes ?: '—' }}</td>
                                    <td class="text-center">
                                        <form method="POST"
                                            action="{{ route('projects.delete-expense', [$project, $expense]) }}"
                                            class="d-inline" onsubmit="return confirm('Delete this expense?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No expenses recorded yet. Add expenses using the form on the left.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Project Notes -->
            @if($project->notes)
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-sticky me-2" style="color:var(--accent);"></i>Project Notes
                    </div>
                    <div class="card-body" style="font-size:13.5px;">{{ $project->notes }}</div>
                </div>
            @endif
        </div>
    </div>
@endsection