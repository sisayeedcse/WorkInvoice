@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="page-header">
        <div>
            <h1>Profile</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Manage your account details and security settings.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card h-100 border-danger-subtle">
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection