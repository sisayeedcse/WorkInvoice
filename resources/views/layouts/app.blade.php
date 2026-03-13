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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1a2b4a;
            --primary-light: #243660;
            --accent: #f47c1c;
            --accent-hover: #e06c10;
            --sidebar-w: 255px;
            --topbar-h: 60px;
            --bg: #f0f2f5;
            --card-bg: #ffffff;
            --text: #1e2530;
            --muted: #6c757d;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            min-height: 100vh;
        }

        /* Sidebar */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: transform .3s ease;
            overflow-y: auto;
        }

        #sidebar::-webkit-scrollbar {
            width: 4px;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .15);
            border-radius: 4px;
        }

        .sidebar-brand {
            padding: 18px 18px 14px;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #fff;
            flex-shrink: 0;
        }

        .brand-name {
            font-size: 12.5px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .brand-sub {
            font-size: 10.5px;
            color: rgba(255, 255, 255, .5);
            margin-top: 1px;
        }

        .nav-section {
            font-size: 9.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, .35);
            padding: 14px 18px 5px;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 18px;
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .18s;
        }

        #sidebar .nav-link i {
            font-size: 15px;
            width: 19px;
            text-align: center;
            flex-shrink: 0;
        }

        #sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, .07);
        }

        #sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, .1);
            border-left-color: var(--accent);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 14px 18px;
            border-top: 1px solid rgba(255, 255, 255, .1);
            font-size: 11px;
            color: rgba(255, 255, 255, .38);
        }

        /* Topbar */
        #topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 22px;
            z-index: 1040;
            gap: 14px;
        }

        .page-title {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text);
            flex: 1;
        }

        /* Main */
        #main {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 26px 22px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* Cards */
        .card {
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .04);
            background: var(--card-bg);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border);
            padding: 15px 18px;
            font-size: 13.5px;
            font-weight: 600;
        }

        .card-body {
            padding: 18px;
        }

        /* Stat cards */
        .stat-card {
            border-radius: 14px;
            padding: 18px 20px;
            color: #fff;
            overflow: hidden;
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            background: rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-card .stat-value {
            font-size: 24px;
            font-weight: 700;
            margin: 8px 0 2px;
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 11.5px;
            opacity: .72;
            font-weight: 500;
        }

        .stat-card.bg-navy {
            background: linear-gradient(135deg, #1a2b4a 0%, #2a3f6e 100%);
        }

        .stat-card.bg-orange {
            background: linear-gradient(135deg, #ea6a10 0%, #f59332 100%);
        }

        .stat-card.bg-green {
            background: linear-gradient(135deg, #15803d 0%, #22c55e 100%);
        }

        .stat-card.bg-blue {
            background: linear-gradient(135deg, #1d4ed8 0%, #60a5fa 100%);
        }

        .stat-card.bg-amber {
            background: linear-gradient(135deg, #92400e 0%, #f59e0b 100%);
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 500;
            font-size: 13px;
        }

        .btn-primary:hover {
            background: var(--primary-light);
            border-color: var(--primary-light);
        }

        .btn-accent {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
            font-weight: 500;
            font-size: 13px;
        }

        .btn-accent:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: #fff;
        }

        .btn-sm {
            font-size: 12px;
            padding: 5px 12px;
        }

        /* Tables */
        .table {
            font-size: 13px;
        }

        .table thead th {
            font-size: 10.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            padding: 9px 12px;
        }

        .table tbody td {
            padding: 12px 12px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .table tbody tr:hover {
            background: #f8fafc;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Forms */
        .form-label {
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text);
        }

        .form-control,
        .form-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            padding: 8px 12px;
            color: var(--text);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(244, 124, 28, .12);
        }

        /* Badges */
        .badge {
            font-size: 10.5px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 5px;
        }

        /* Alerts */
        .alert {
            font-size: 13px;
            border-radius: 10px;
        }

        /* Page header */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .page-header h1 {
            font-size: 19px;
            font-weight: 700;
            margin: 0;
        }

        /* Doc number */
        .doc-number {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 13px;
            color: var(--primary);
        }

        /* Sidebar overlay (mobile) */
        #sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 1045;
        }

        /* Mobile */
        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.open {
                transform: translateX(0);
            }

            #sidebar-overlay.show {
                display: block;
            }

            #topbar {
                left: 0;
            }

            #main {
                margin-left: 0;
                padding: 18px 14px;
            }

            .stat-card .stat-value {
                font-size: 20px;
            }

            .page-header h1 {
                font-size: 16px;
            }

            .table-responsive-sm {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }

        /* Items row in form */
        .item-row td input,
        .item-row td select {
            font-size: 12.5px;
            padding: 6px 8px;
        }

        .remove-row {
            cursor: pointer;
            color: #ef4444;
            font-size: 16px;
            line-height: 1;
        }

        .remove-row:hover {
            color: #b91c1c;
        }

        /* 5-column grid (20% each) */
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        /* DataTables overrides */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            padding: 6px 10px;
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(244, 124, 28, .12);
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 13px;
            padding: 5px 8px;
            color: var(--text);
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 12.5px;
            color: var(--muted);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px !important;
            font-size: 12.5px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background: var(--bg) !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
        }

        /* Print styles */
        @media print {
            #sidebar, #topbar { display: none !important; }
            .main-content { margin-left: 0 !important; padding-top: 16px !important; }
            .page-header .btn, .page-header form, .print-hide { display: none !important; }
            .card { border: 1px solid #ccc !important; box-shadow: none !important; break-inside: avoid; }
            body { background: #fff !important; }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div id="sidebar-overlay" onclick="closeSidebar()"></div>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            @php $companyLogo = \App\Models\Setting::get('company_logo'); @endphp
            @if($companyLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($companyLogo))
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($companyLogo) }}"
                 alt="Logo"
                 style="height:36px;max-width:120px;object-fit:contain;border-radius:4px;">
            @else
            <div class="brand-icon"><i class="bi bi-hammer"></i></div>
            @endif
            <div>
                <div class="brand-name">Al Anamil Workshop</div>
                <div class="brand-sub">Management System</div>
            </div>
        </div>

        <div class="pt-1 pb-3">
            <div class="nav-section">Main</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section">Business</div>
            <a href="{{ route('customers.index') }}"
                class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Customers
            </a>
            <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Services / Items
            </a>

            <div class="nav-section">Documents</div>
            <a href="{{ route('quotations.index') }}"
                class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Quotations
            </a>
            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check-fill"></i> Orders
            </a>
            <a href="{{ route('purchase-orders.index') }}"
                class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                <i class="bi bi-cart-fill"></i> Purchase Orders
            </a>
            <a href="{{ route('invoices.index') }}"
                class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="bi bi-receipt-cutoff"></i> Invoices
            </a>

            <div class="nav-section">Analytics</div>
            <a href="{{ route('reports.index') }}"
                class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line-fill"></i> Reports
            </a>

            <div class="nav-section">System</div>
            <a href="{{ route('settings.index') }}"
                class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Settings
            </a>
        </div>

        <div class="sidebar-footer">
            <div style="color:rgba(255,255,255,.55);font-size:11.5px;font-weight:600;">{{ config('company.name') }}
            </div>
            <div style="margin-top:2px;">{{ config('company.phone') }}</div>
        </div>
    </nav>

    <!-- Topbar -->
    <header id="topbar">
        <button class="btn btn-sm d-md-none me-1 border-0 p-1" onclick="openSidebar()" style="color:var(--primary);">
            <i class="bi bi-list" style="font-size:22px;"></i>
        </button>

        <div class="page-title">@yield('page-title', 'Dashboard')</div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('quotations.create') }}" class="btn btn-accent btn-sm rounded-pill">
                <i class="bi bi-plus-lg me-1"></i>
                <span class="d-none d-sm-inline">New Quote</span>
            </a>

            <div class="dropdown">
                <button class="btn btn-sm border-0 d-flex align-items-center gap-2 p-1" data-bs-toggle="dropdown">
                    <div
                        style="width:30px;height:30px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline"
                        style="font-size:13px;font-weight:500;">{{ auth()->user()->name }}</span>
                    <i class="bi bi-chevron-down d-none d-md-inline" style="font-size:10px;color:var(--muted);"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3"
                    style="font-size:13px;min-width:160px;">
                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i
                                class="bi bi-person me-2 text-muted"></i>Profile</a></li>
                    <li>
                        <hr class="dropdown-divider my-1">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main id="main">

        @if (session('success'))
            <div class="alert d-flex align-items-center gap-2 mb-4 alert-dismissible fade show"
                style="background:#dcfce7;color:#15803d;border:1px solid #bbf7d0;" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert d-flex align-items-center gap-2 mb-4 alert-dismissible fade show"
                style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;" role="alert">
                <i class="bi bi-exclamation-circle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert d-flex align-items-center gap-2 mb-4 alert-dismissible fade show"
                style="background:#dbeafe;color:#1d4ed8;border:1px solid #bfdbfe;" role="alert">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert mb-4 alert-dismissible fade show"
                style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca;" role="alert">
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
        function openSidebar() { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebar-overlay').classList.add('show'); }
        function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebar-overlay').classList.remove('show'); }
    </script>
    @stack('scripts')
</body>

</html>