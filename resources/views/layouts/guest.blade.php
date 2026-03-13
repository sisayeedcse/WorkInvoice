<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Al Anamil Workshop Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a2b4a 0%, #243660 50%, #1a2b4a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 60px rgba(0,0,0,.35);
        }

        .brand-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #f47c1c, #e06c10);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; color: #fff;
            margin: 0 auto 16px;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            color: #1a2b4a;
            text-align: center;
            line-height: 1.3;
        }

        .brand-sub {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
            margin-top: 3px;
        }

        .auth-card h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1e2530;
            margin-top: 24px;
            margin-bottom: 6px;
        }

        .auth-card p.lead {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 24px;
        }

        .form-label { font-size: 12.5px; font-weight: 600; color: #1e2530; }
        .form-control { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; font-size: 13.5px; }
        .form-control:focus { border-color: #f47c1c; box-shadow: 0 0 0 3px rgba(244,124,28,.12); }

        .btn-login {
            background: linear-gradient(135deg, #1a2b4a, #243660);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 11px;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: opacity .2s;
        }

        .btn-login:hover { opacity: .88; color: #fff; }

        .divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }

        .system-note {
            text-align: center;
            font-size: 11.5px;
            color: #adb5bd;
            margin-top: 20px;
        }

        .form-check-input:checked { background-color: #1a2b4a; border-color: #1a2b4a; }
        .invalid-feedback { font-size: 11.5px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="brand-logo"><i class="bi bi-hammer"></i></div>
        <div class="brand-name">Al Anamil Al Thahabiah<br>Steel Workshop</div>
        <div class="brand-sub">Kalba, Sharjah · UAE</div>

        <h2>Welcome Back</h2>
        <p class="lead">Sign in to your workshop manager</p>

        {{ $slot }}

        <div class="system-note">
            <i class="bi bi-shield-lock me-1"></i>
            Private internal system &mdash; authorized personnel only
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
