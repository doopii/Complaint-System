<!DOCTYPE html>
<html>
<head>
    <title>FixIt</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
</head>
<body>
    <!-- Site Header -->
    <header class="navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="logo">FixIt</a>
                <nav class="navbar-center">
                    <a href="{{ route('home') }}" class="active">Home</a>
                    @auth
                        @if(Auth::user()->isStudent())
                            <a href="{{ route('complaints.dashboard') }}">My Complaints</a>
                            <a href="{{ route('student.community') }}">Community</a>
                            <a href="{{ route('complaints.create') }}">Submit Complaint</a>
                        @elseif(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
                    @endauth
                </nav>
            </div>
            <div class="navbar-right">
                @auth
                    <span class="user-info">{{ Auth::user()->name ?? Auth::user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn logout-btn">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <div class="form-container">
        <h1>Welcome to FixIt</h1>
        
        @auth
            <div class="welcome-section">
                <h2>Hello, {{ Auth::user()->name }}!</h2>
                
                @if(Auth::user()->isAdmin())
                    <div class="action-cards">
                        <a href="{{ route('admin.dashboard') }}" class="action-card admin-card">
                            <h3>Admin Dashboard</h3>
                            <p>Manage and process student complaints</p>
                        </a>
                    </div>
                @else
                    <div class="action-cards">
                        <a href="{{ route('complaints.dashboard') }}" class="action-card student-card">
                            <h3>My Complaints</h3>
                            <p>View and manage your submitted complaints</p>
                        </a>
                        <a href="{{ route('complaints.create') }}" class="action-card create-card">
                            <h3>Submit New Complaint</h3>
                            <p>Report a new issue or concern</p>
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="welcome-section">
                <p>Submit and track complaints about campus facilities, academics, security, and other concerns.</p>
                
                <div class="action-cards">
                    <a href="{{ route('login') }}" class="action-card login-card">
                        <h3>Login</h3>
                        <p>Access your existing account</p>
                    </a>
                    <a href="{{ route('register') }}" class="action-card register-card">
                        <h3>Register</h3>
                        <p>Create a new account</p>
                    </a>
                </div>
            </div>
        @endauth
    </div>
</body>
</html>
