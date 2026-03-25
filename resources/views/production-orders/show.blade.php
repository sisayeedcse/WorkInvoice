@extends('layouts.app')
@section('title', $productionOrder->production_number)
@section('page-title', $productionOrder->production_number)

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-gear-wide-connected"></i> Production Order</div>
            <h1 class="hero-title">{{ $productionOrder->production_number }}</h1>
            <p class="hero-copy">Track status and material consumption for this production run.</p>
        </div>
        <div class="hero-actions">
            @if($productionOrder->status === 'pending')
                <a href="{{ route('production-orders.edit', $productionOrder) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
                <form method="POST" action="{{ route('production-orders.start', $productionOrder) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-primary btn-sm"><i class="bi bi-play-fill me-1"></i>Start</button>
                </form>
            @endif
            @if(in_array($productionOrder->status, ['pending', 'in_progress']))
                <form method="POST" action="{{ route('production-orders.complete', $productionOrder) }}" class="d-inline" onsubmit="return confirm('Complete production and update stock?')">
                    @csrf
                    <button class="btn btn-success btn-sm"><i class="bi bi-check2-circle me-1"></i>Complete</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Finished Product</div><div class="summary-value">{{ $productionOrder->finishedItem?->name }}</div></div></div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Target Qty</div><div class="summary-value">{{ number_format($productionOrder->quantity_to_produce, 3) }}</div></div></div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Produced Qty</div><div class="summary-value">{{ number_format($productionOrder->quantity_produced, 3) }}</div></div></div></div></div>
        <div class="col-md-3"><div class="summary-card"><div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Status</div><div class="summary-value">{{ ucfirst(str_replace('_',' ', $productionOrder->status)) }}</div></div></div></div></div>
    </div>

    <div class="table-card">
        <div class="table-card-header"><span><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Material Usage</span></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th class="text-center">Qty Used</th>
                        <th class="text-center">Unit</th>
                        <th class="text-end">Unit Cost</th>
                        <th class="text-end">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productionOrder->materials as $material)
                        <tr>
                            <td>{{ $material->item_name }}</td>
                            <td class="text-center">{{ number_format($material->quantity_required, 3) }}</td>
                            <td class="text-center">{{ $material->unit }}</td>
                            <td class="text-end">{{ $material->unit_cost !== null ? 'AED ' . number_format($material->unit_cost, 2) : '—' }}</td>
                            <td class="text-end">{{ $material->total_cost !== null ? 'AED ' . number_format($material->total_cost, 2) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No materials found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
