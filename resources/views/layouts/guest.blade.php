<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') — Al Anamil Workshop Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/main.css', 'resources/js/app.js'])
    <style>
        body.auth-page {
            background: radial-gradient(circle at 20% 0%, #dde6ff 0%, #f4f6fb 48%, #eef2f8 100%);
            min-height: 100vh;
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(280px, 460px) minmax(320px, 520px);
            justify-content: center;
            align-items: center;
            gap: 24px;
            padding: 24px 16px;
        }

        .auth-brand-panel {
            background: linear-gradient(160deg, #121a3f 0%, #1f2f87 52%, #2f43ce 100%);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 34px 30px;
            box-shadow: 0 24px 52px rgba(16, 24, 40, 0.2);
        }

        .brand-logo-box {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: linear-gradient(145deg, #f09a28, #db7b16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 14px 20px rgba(219, 123, 22, 0.28);
            margin-bottom: 16px;
        }

        .auth-brand-panel h1 {
            margin: 0;
            color: #fff;
            font-size: 22px;
            line-height: 1.3;
            font-weight: 800;
        }

        .auth-brand-panel p {
            margin: 12px 0 0;
            color: rgba(231, 239, 255, 0.88);
            font-size: 13px;
            line-height: 1.6;
        }

        .auth-card {
            background: #fff;
            border: 1px solid var(--border-primary);
            border-radius: 24px;
            padding: 30px 28px;
            box-shadow: 0 20px 48px rgba(16, 24, 40, 0.12);
        }

        .auth-card .auth-title {
            font-size: 21px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .auth-card .auth-subtitle {
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 20px;
        }

        .btn-login {
            width: 100%;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(145deg, var(--primary-700), var(--primary-500));
        }

        .auth-system-note {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px solid var(--border-light);
            color: var(--gray-500);
            font-size: 11.5px;
            text-align: center;
        }

        @media (max-width: 991px) {
            .auth-shell {
                grid-template-columns: 1fr;
                max-width: 520px;
                margin: 0 auto;
                gap: 14px;
            }

            .auth-brand-panel {
                padding: 20px;
                border-radius: 18px;
            }

            .auth-card {
                border-radius: 18px;
                padding: 22px 18px;
            }
        }
    </style>
</head>

<body class="auth-page">
    <div class="auth-shell">
        <aside class="auth-brand-panel">
            <div class="brand-logo-box"><i class="bi bi-hammer"></i></div>
            <h1>Al Anamil Workshop Manager</h1>
            <p>Professional workflow platform for quotations, orders, invoices, reporting, and operations management.
            </p>
            <p class="mb-0 mt-3"><i class="bi bi-geo-alt me-1"></i>Kalba, Sharjah · UAE</p>
        </aside>

        <main class="auth-card">
            <div class="auth-title">Secure Access</div>
            <div class="auth-subtitle">Use your account credentials to continue.</div>

            {{ $slot }}

            <div class="auth-system-note">
                <i class="bi bi-shield-lock me-1"></i>
                Private internal system — authorized personnel only
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>