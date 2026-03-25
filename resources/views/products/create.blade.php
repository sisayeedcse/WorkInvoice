@extends('layouts.app')
@section('title', 'Add Product')
@section('page-title', 'Add Product')

@section('content')
    <div class="page-header">
        <div>
            <h1>Add Product</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Create a stock-tracked or non-stock product for POS and
                production usage.</p>
        </div>
    </div>

    <div class="form-main">
        <div class="card">
            <div class="card-header"><i class="bi bi-box-seam me-2" style="color:var(--accent);"></i>Product Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('products.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="item_type" class="form-select" required>
                                @foreach(['trading' => 'Trading', 'manufactured' => 'Manufactured', 'raw_material' => 'Raw Material', 'service' => 'Service'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('item_type', 'trading') === $value ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Sell Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="default_price" class="form-control"
                                value="{{ old('default_price', '0') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Buy Price</label>
                            <input type="number" step="0.01" min="0" name="cost_price" class="form-control"
                                value="{{ old('cost_price', '0') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit', 'Unit') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Initial Stock</label>
                            <input type="number" step="0.001" min="0" name="stock_quantity" class="form-control"
                                value="{{ old('stock_quantity', '0') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Minimum Stock</label>
                            <input type="number" step="0.001" min="0" name="reorder_level" class="form-control"
                                value="{{ old('reorder_level') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="track_inventory" id="track_inventory"
                                    value="1" {{ old('track_inventory', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="track_inventory">Track Inventory</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Save
                            Product</button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection