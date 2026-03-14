<x-guest-layout>
    @if (session('status'))
        <div class="alert alert-success py-2 px-3 mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <input id="email" name="email" type="email"
                class="form-control @if($errors->has('email')) is-invalid @endif" value="{{ old('email') }}"
                autocomplete="username" placeholder="your@email.com" required autofocus>
            @if($errors->has('email'))
                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label class="form-label mb-0" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-muted"
                        style="font-size:12px;text-decoration:none;">Forgot password?</a>
                @endif
            </div>
            <input id="password" name="password" type="password"
                class="form-control @if($errors->has('password')) is-invalid @endif" autocomplete="current-password"
                placeholder="••••••••" required>
            @if($errors->has('password'))
                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
            @endif
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
            <label class="form-check-label" for="remember_me" style="font-size:13px;color:#6c757d;">Keep me signed
                in</label>
        </div>

        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>
</x-guest-layout>