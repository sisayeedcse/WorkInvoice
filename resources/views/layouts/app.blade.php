<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Al Anamil Workshop Manager</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Vite CSS -->
    @vite(['resources/css/main.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
    @php
        $companyLogo = \App\Models\Setting::get('company_logo');
        $hasCompanyLogo = $companyLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($companyLogo);
    @endphp

    <div id="sidebar-overlay" onclick="closeSidebar()"></div>

    <nav id="sidebar" aria-label="Primary navigation">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            @if($hasCompanyLogo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($companyLogo) }}" alt="Company Logo"
                    class="brand-logo">
            @else
                <div class="brand-icon"><i class="bi bi-hammer"></i></div>
            @endif
            <div>
                <div class="brand-name">Al Anamil Workshop</div>
                <div class="brand-sub">Management System</div>
            </div>
        </div>

        <div class="pt-2 pb-3">
            <div class="nav-section">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
            </a>

            <div class="nav-section">Business</div>
            <a href="{{ route('customers.index') }}"
                class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i><span>Customers</span>
            </a>
            <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i><span>Services / Items</span>
            </a>

            <div class="nav-section">Documents</div>
            <a href="{{ route('quotations.index') }}"
                class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i><span>Quotations</span>
            </a>
            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i><span>Orders</span>
            </a>
            <a href="{{ route('purchase-orders.index') }}"
                class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-fill"></i><span>Purchase Orders</span>
            </a>
            <a href="{{ route('invoices.index') }}"
                class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i><span>Invoices</span>
            </a>

            <div class="nav-section">Analytics</div>
            <a href="{{ route('reports.index') }}"
                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill"></i><span>Reports</span>
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('settings.index') }}"
                class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i><span>Settings</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-company-name">{{ config('company.name') }}</div>
            <div>{{ config('company.phone') }}</div>
        </div>
    </nav>

    <header id="topbar">
        <button class="btn btn-icon btn-light d-md-none" onclick="openSidebar()" aria-label="Open navigation">
            <i class="bi bi-list"></i>
        </button>

        <div class="page-title">@yield('page-title', 'Dashboard')</div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('quotations.create') }}" class="btn btn-accent btn-sm rounded-pill">
                <i class="bi bi-plus-lg"></i>
                <span class="d-none d-sm-inline">New Quote</span>
            </a>

            <div class="dropdown">
                <button class="btn btn-light btn-sm user-menu-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="d-none d-md-inline user-name">{{ auth()->user()->name }}</span>
                    <i class="bi bi-chevron-down d-none d-md-inline"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person"></i>Profile
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main id="main">

        @if (session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info d-flex align-items-center gap-2 mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4 alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Please fix the errors below.</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebar-overlay').classList.add('show');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.remove('show');
        }
    </script>
    @stack('scripts')
</body>

</html>