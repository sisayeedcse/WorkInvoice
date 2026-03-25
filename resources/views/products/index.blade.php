@extends('layouts.app')
@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-box-seam"></i> Inventory</div>
            <h1 class="hero-title">Products</h1>
            <p class="hero-copy">Manage trading products, raw materials, and manufactured goods with live stock visibility.
            </p>
        </div>
        <div class="hero-actions">
            <a href="{{ route('products.create') }}" class="btn btn-accent"><i class="bi bi-plus-lg"></i> Add Product</a>
        </div>
    </div>

    <div class="toolbar-card mb-3">
        <div class="toolbar-copy">Filter products by type, stock status, and name/SKU.</div>
        <div class="toolbar-actions">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach(['trading' => 'Trading', 'manufactured' => 'Manufactured', 'raw_material' => 'Raw Material', 'service' => 'Service'] as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="stock_status" class="form-select form-select-sm" style="width:auto;"
                    onchange="this.form.submit()">
                    <option value="">All Stock</option>
                    <option value="low" {{ request('stock_status') === 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('stock_status') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                    style="width:200px;" placeholder="Name or SKU">
                <button class="btn btn-sm btn-primary" type="submit">Apply</button>
                @if(request()->filled('type') || request()->filled('stock_status') || request()->filled('search'))
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table id="productsTable" class="table mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Type</th>
                        <th>Sell Price</th>
                        <th>Buy Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td><a href="{{ route('products.show', $product) }}"
                                    class="doc-number text-decoration-none">{{ $product->name }}</a></td>
                            <td class="text-muted">{{ $product->sku ?: '—' }}</td>
                            <td>{!! $product->item_type_badge !!}</td>
                            <td>AED {{ number_format($product->default_price, 2) }}</td>
                            <td>{{ $product->cost_price !== null ? 'AED ' . number_format($product->cost_price, 2) : '—' }}</td>
                            <td>{{ number_format($product->stock_quantity, 3) }} {{ $product->unit }}</td>
                            <td>{!! $product->low_stock_badge !!}</td>
                            <td class="text-end">
                                <a href="{{ route('products.adjust-stock', $product) }}" class="btn btn-sm btn-light me-1"
                                    title="Adjust Stock"><i class="bi bi-arrow-repeat"></i></a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-light me-1"
                                    title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="d-inline"
                                    onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Delete"><i
                                            class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-icon"><i class="bi bi-box-seam"></i></div>
                                <div class="empty-state-copy">No products found.</div>
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
        new DataTable('#productsTable', {
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [{ orderable: false, targets: [7] }],
            language: {
                search: '',
                searchPlaceholder: 'Search products...',
                lengthMenu: 'Show _MENU_ products',
                info: 'Showing _START_–_END_ of _TOTAL_ products'
            },
            dom: "<'row mb-3 px-3'<'col-sm-6'l><'col-sm-6 d-flex justify-content-end'f>>rtip"
        });
    </script>
@endpush