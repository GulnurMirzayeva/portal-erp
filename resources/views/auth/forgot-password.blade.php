<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Forgot Password - Portal ERP</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<div class="erp-login-page">

    <div class="erp-login-wrapper">

        <div class="erp-login-card">

            <div class="erp-login-header">

                <div class="erp-login-brand">
                    <h1>Forgot Password?</h1>

                    <p>
                        Enter your email to receive a password reset link.
                    </p>
                </div>

                <div class="erp-login-decoration"></div>

            </div>

            <div class="erp-login-body">

                @if (session('status'))
                    <div class="erp-login-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="erp-login-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">

                    @csrf

                    <div class="erp-form-group">

                        <label
                            for="email"
                            class="erp-form-label"
                        >
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="erp-form-input"
                            placeholder="Enter your email"
                            required
                            autofocus
                        >

                    </div>

                    <button
                        type="submit"
                        class="erp-login-button"
                    >
                        Send Reset Link
                    </button>

                </form>

            </div>

            <div class="erp-login-footer">

                <p>
                    Remember your password?
                    <a href="{{ route('login') }}">
                        Back to login
                    </a>
                </p>

                <p class="erp-login-copyright">
                    © {{ date('Y') }} Portal ERP. All rights reserved.
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>