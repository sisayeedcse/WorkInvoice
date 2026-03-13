@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="page-header">
        <div>
            <h1>Settings</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Manage company information and system preferences</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                @csrf @method('PATCH')

                <!-- Company Logo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-image me-2" style="color:var(--accent);"></i>Company Logo
                    </div>
                    <div class="card-body">
                        @if($company['logo'])
                            <div class="mb-3 d-flex align-items-center gap-3">
                                <img src="{{ Storage::disk('public')->url($company['logo']) }}" alt="Company Logo"
                                    style="height:64px;max-width:220px;object-fit:contain;border:1px solid var(--border);border-radius:8px;padding:6px;background:#f8fafc;">
                                <div>
                                    <div style="font-size:13px;font-weight:600;color:var(--text);">Current logo</div>
                                    <label class="d-flex align-items-center gap-2 mt-1"
                                        style="font-size:13px;cursor:pointer;color:#ef4444;">
                                        <input type="checkbox" name="remove_logo" value="1" class="form-check-input"
                                            style="margin:0;">
                                        Remove this logo
                                    </label>
                                </div>
                            </div>
                        @endif
                        <div>
                            <label class="form-label">{{ $company['logo'] ? 'Replace Logo' : 'Upload Logo' }}</label>
                            <input type="file" name="company_logo"
                                class="form-control @error('company_logo') is-invalid @enderror"
                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp">
                            @error('company_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">PNG, JPG or SVG. Max 2 MB. Recommended: 300×80 px transparent PNG.</div>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-building me-2" style="color:var(--accent);"></i>Company Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $company['name'] }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Owner / Manager</label>
                                <input type="text" name="company_owner" class="form-control"
                                    value="{{ $company['owner'] }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Tagline</label>
                                <input type="text" name="company_tagline" class="form-control"
                                    value="{{ $company['tagline'] }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="company_address" class="form-control"
                                    rows="2">{{ $company['address'] }}</textarea>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="company_phone" class="form-control"
                                    value="{{ $company['phone'] }}">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="company_email" class="form-control"
                                    value="{{ $company['email'] }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-currency-dollar me-2" style="color:var(--accent);"></i>Currency Settings
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Currency Code</label>
                                <input type="text" name="currency" class="form-control" value="{{ $company['currency'] }}"
                                    placeholder="e.g. AED, USD">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Currency Symbol</label>
                                <input type="text" name="currency_symbol" class="form-control"
                                    value="{{ $company['currency_symbol'] }}" placeholder="e.g. AED, $">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-file-text me-2" style="color:var(--accent);"></i>Default Terms & Conditions
                    </div>
                    <div class="card-body">
                        <label class="form-label">These will appear on all quotations and invoices</label>
                        <textarea name="default_terms" class="form-control" rows="6">{{ $company['terms'] }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-accent px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Settings
                </button>
            </form>
        </div>

        <div class="col-12 col-lg-4">
            <!-- Company Preview Card -->
            <div class="card mb-3 sticky-top" style="top:80px;">
                <div class="card-header"><i class="bi bi-eye me-2" style="color:var(--accent);"></i>Document Header Preview
                </div>
                <div class="card-body" style="background:#f8fafc;border-radius:0 0 12px 12px;">
                    <div style="border:1px solid var(--border);border-radius:10px;padding:16px;background:#fff;">
                        <div style="display:flex;align-items:center;gap:14px;margin-bottom:12px;">
                            @if($company['logo'])
                                <img src="{{ Storage::disk('public')->url($company['logo']) }}" alt="Logo"
                                    style="height:48px;max-width:160px;object-fit:contain;">
                            @else
                                <div
                                    style="width:48px;height:48px;background:var(--primary);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;">
                                    <i class="bi bi-hammer"></i>
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:700;font-size:14px;color:var(--primary);">{{ $company['name'] }}
                                </div>
                                <div style="font-size:11px;color:var(--muted);">{{ $company['tagline'] }}</div>
                            </div>
                        </div>
                        <div style="font-size:11px;color:var(--muted);line-height:1.6;">
                            <div><i class="bi bi-geo-alt me-1"></i>{{ $company['address'] }}</div>
                            <div><i class="bi bi-telephone me-1"></i>{{ $company['phone'] }}</div>
                            <div><i class="bi bi-envelope me-1"></i>{{ $company['email'] }}</div>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0" style="font-size:11.5px;">
                        <i class="bi bi-info-circle me-1"></i>
                        This information appears on all PDF documents generated by the system.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection