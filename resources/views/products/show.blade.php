@extends('layouts.app')
@section('title', $product->name)
@section('page-title', 'Product Details')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-box-seam"></i> Product Record</div>
            <h1 class="hero-title">{{ $product->name }}</h1>
            <p class="hero-copy">Review current stock, pricing, and movement history.</p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('products.adjust-stock', $product) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Adjust Stock</a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Type</div><div class="summary-value">{{ ucfirst(str_replace('_', ' ', $product->item_type)) }}</div></div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Current Stock</div><div class="summary-value">{{ number_format($product->stock_quantity, 3) }}</div></div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Sell Price</div><div class="summary-value">{{ number_format($product->default_price, 2) }}</div></div></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-card-head"><div class="summary-card-body"><div class="summary-label">Min Stock</div><div class="summary-value">{{ $product->reorder_level !== null ? number_format($product->reorder_level, 3) : '—' }}</div></div></div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-card-header">
            <span><i class="bi bi-clock-history me-2" style="color:var(--accent);"></i>Recent Stock Movements</span>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Reference</th>
                        <th>Balance After</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->stockMovements as $movement)
                        <tr>
                            <td>{{ $movement->movement_date?->format('d M Y H:i') }}</td>
                            <td>{!! $movement->movement_type_badge !!}</td>
                            <td>{{ number_format($movement->quantity, 3) }}</td>
                            <td>{{ $movement->reference_type_label }}</td>
                            <td>{{ number_format($movement->balance_after, 3) }}</td>
                            <td class="text-muted">{{ $movement->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No stock movement history yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
