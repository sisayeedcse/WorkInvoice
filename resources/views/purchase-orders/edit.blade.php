@extends('layouts.app')
@section('title', 'Edit Purchase Order')
@section('page-title', 'Edit Purchase Order')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('purchase-orders.index') }}">Purchase Orders</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
        <h1>Edit {{ $purchaseOrder->po_number }}</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2" style="color:var(--accent);"></i>Purchase Order Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name', $purchaseOrder->supplier_name) }}" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['draft','sent','received','cancelled'] as $s)
                                <option value="{{ $s }}" {{ old('status', $purchaseOrder->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="supplier_phone" class="form-control" value="{{ old('supplier_phone', $purchaseOrder->supplier_phone) }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="supplier_email" class="form-control" value="{{ old('supplier_email', $purchaseOrder->supplier_email) }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">PO Date</label>
                            <input type="date" name="date" class="form-control" value="{{ old('date', $purchaseOrder->date->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', optional($purchaseOrder->delivery_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Discount (AED)</label>
                            <input type="number" name="discount" class="form-control" step="0.01" min="0"
                                   value="{{ old('discount', $purchaseOrder->discount) }}">
                            <div class="form-text">Applied to grand total after subtotal.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Update PO</button>
                        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
