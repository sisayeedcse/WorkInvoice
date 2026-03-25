@extends('layouts.app')
@section('title', 'Adjust Stock')
@section('page-title', 'Adjust Stock')

@section('content')
    <div class="page-header">
        <div>
            <h1>Adjust Stock: {{ $product->name }}</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Set the new stock quantity for this product. A stock movement
                record will be created automatically.</p>
        </div>
    </div>

    <div class="form-main">
        <div class="card">
            <div class="card-header"><i class="bi bi-arrow-repeat me-2" style="color:var(--accent);"></i>Stock Adjustment
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('products.update-stock', $product) }}">
                    @csrf @method('PATCH')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Current Stock</label>
                            <input type="text" class="form-control"
                                value="{{ number_format($product->stock_quantity, 3) }} {{ $product->unit }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">New Quantity <span class="text-danger">*</span></label>
                            <input type="number" step="0.001" min="0" name="new_quantity" class="form-control"
                                value="{{ old('new_quantity', $product->stock_quantity) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"
                                placeholder="Reason for adjustment">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Update
                            Stock</button>
                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection