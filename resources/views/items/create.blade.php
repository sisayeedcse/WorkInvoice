@extends('layouts.app')
@section('title', 'Add Service')
@section('page-title', 'Add Service')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Services</a></li>
            <li class="breadcrumb-item active">New Service</li>
        </ol></nav>
        <h1>Add New Service</h1>
        <p class="text-muted mb-0" style="font-size:13px;">Build a reusable pricing entry for faster document creation.</p>
    </div>
</div>

<div class="form-shell">
    <aside class="form-sidebar">
        <div class="surface-note">
            <strong>Library quality matters</strong><br>
            Clear names, realistic default prices, and sensible categories improve quotation speed and reduce pricing mistakes.
        </div>
    </aside>

    <div class="form-main">
        <div class="card">
            <div class="card-header"><i class="bi bi-tools me-2" style="color:var(--accent);"></i>Service Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('items.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Service Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Main Gate Fabrication" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Default Price (AED) <span class="text-danger">*</span></label>
                            <input type="number" name="default_price" step="0.01" min="0"
                                   class="form-control @error('default_price') is-invalid @enderror"
                                   value="{{ old('default_price', '0.00') }}" required>
                            @error('default_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 col-sm-6">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit" class="form-select">
                                @foreach(['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'] as $u)
                                <option value="{{ $u }}" {{ old('unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control"
                                   value="{{ old('category') }}" placeholder="e.g. Gate, Door, Staircase">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Optional description of this service...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Save Service</button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
