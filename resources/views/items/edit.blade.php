@extends('layouts.app')
@section('title', 'Edit Service')
@section('page-title', 'Edit Service')

@section('content')
    <div class="page-header">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Services</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1>Edit: {{ $item->name }}</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Refine pricing and descriptions to keep your library clean.
            </p>
        </div>
    </div>

    <div class="form-shell">
        <aside class="form-sidebar">
            <div class="summary-card">
                <div class="summary-card-head">
                    <div class="summary-card-body">
                        <div class="summary-label">Unit</div>
                        <div class="summary-value">{{ $item->unit }}</div>
                    </div>
                    <div class="summary-icon"><i class="bi bi-rulers"></i></div>
                </div>
                <div class="summary-foot">Current pricing unit</div>
            </div>
        </aside>

        <div class="form-main">
            <div class="card">
                <div class="card-header"><i class="bi bi-pencil-square me-2" style="color:var(--accent);"></i>Service
                    Details</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('items.update', $item) }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Service Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $item->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Default Price (AED) <span class="text-danger">*</span></label>
                                <input type="number" name="default_price" step="0.01" min="0" class="form-control"
                                    value="{{ old('default_price', $item->default_price) }}" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select">
                                    @foreach(['Unit', 'Piece', 'Meter', 'Sqm', 'Kg', 'Set', 'Lot', 'Hour', 'Day', 'Job'] as $u)
                                        <option value="{{ $u }}" {{ old('unit', $item->unit) == $u ? 'selected' : '' }}>{{ $u }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control"
                                    value="{{ old('category', $item->category) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control"
                                    rows="3">{{ old('description', $item->description) }}</textarea>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Update
                                Service</button>
                            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection