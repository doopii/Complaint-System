<!DOCTYPE html>
<html>
<head>
    <title>Login - FixIt</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/auth-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>
<body>
    <!-- Site Header -->
    <header class="navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="logo">FixIt</a>
            </div>
            <nav class="navbar-center">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('login') }}" class="active">Login</a>
                <a href="{{ route('register') }}">Register</a>
            </nav>
            <div class="navbar-right">
            </div>
        </div>
    </header>

    <div class="form-container">
        <h2>Login to Your Account</h2>
        
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="complaint-form">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <div class="auth-links">
            <p>Don't have an account? <a href="{{ route('register') }}">Register here</a></p>
        </div>
    </div>
</body>
</html>
