@extends('layouts.app')
@section('title', 'Edit Order')
@section('page-title', 'Edit Order')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
        <h1>Edit {{ $order->order_number }}</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2" style="color:var(--accent);"></i>Order Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('orders.update', $order) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Customer <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-select" required>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $order->customer_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}{{ $c->company_name ? ' ('.$c->company_name.')' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','approved','in_progress','completed','delivered','cancelled'] as $s)
                                <option value="{{ $s }}" {{ old('status', $order->status) == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', optional($order->delivery_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Delivery Information</label>
                            <textarea name="delivery_info" class="form-control" rows="2">{{ old('delivery_info', $order->delivery_info) }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Update Order</button>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
