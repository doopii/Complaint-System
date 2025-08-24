<!DOCTYPE html>
<html>
<head>
    <title>Higher Management - FixIt</title>
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
            </div>
            <nav class="navbar-center">
                <a href="{{ route('home') }}">Home</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        <a href="{{ route('higher.management.index') }}" class="active">Higher Management</a>
                    @elseif(auth()->user()->isStudent())
                        <a href="{{ route('complaints.dashboard') }}">My Complaints</a>
                        <a href="{{ route('complaints.create') }}">Submit Complaint</a>
                    @endif
                @else
                    <a href="{{ route('login') }}">Login</a>
                @endauth
            </nav>
            <div class="navbar-right">
                @auth
                    <span class="user-info">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn logout-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn">Login</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="form-container">
        <h2>Higher Management Dashboard</h2>
        <p class="section-description">Access advanced management tools and analytics for comprehensive oversight of the complaint system.</p>
        
        <!-- Main Options Grid -->
        <div class="options-grid">
            <!-- Analytics Dashboard -->
            <div class="option-card">
                <div class="option-icon">📊</div>
                <h3>Analytics Dashboard</h3>
                <p>Comprehensive insights into complaint trends, performance metrics, and system analytics.</p>
                <div class="option-features">
                    <span class="feature-tag">Statistics</span>
                    <span class="feature-tag">Trends</span>
                    <span class="feature-tag">Reports</span>
                </div>
                <a href="{{ route('higher.management.analytics') }}" class="option-btn">Access Analytics</a>
            </div>

            <!-- Notification Settings -->
            <div class="option-card">
                <div class="option-icon">🔔</div>
                <h3>Notification Settings</h3>
                <p>Configure system-wide notification preferences and alert management for administrators.</p>
                <div class="option-features">
                    <span class="feature-tag">Alerts</span>
                    <span class="feature-tag">Preferences</span>
                    <span class="feature-tag">Management</span>
                </div>
                <a href="{{ route('higher.management.notifications') }}" class="option-btn">Manage Notifications</a>
            </div>

            <!-- Unresolved Issues -->
            <div class="option-card">
                <div class="option-icon">⚠️</div>
                <h3>Unresolved Issues</h3>
                <p>Monitor and manage all pending and in-progress complaints requiring attention.</p>
                <div class="option-features">
                    <span class="feature-tag">Monitoring</span>
                    <span class="feature-tag">Priority</span>
                    <span class="feature-tag">Escalation</span>
                </div>
                <a href="{{ route('higher.management.unresolved') }}" class="option-btn">View Issues</a>
            </div>
        </div>

        <!-- Quick Stats Overview -->
        <div class="quick-stats-section">
            <h3>Quick Overview</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label">Total Complaints</span>
                    <span class="stat-value">{{ \App\Models\Complaint::count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Pending</span>
                    <span class="stat-value pending">{{ \App\Models\Complaint::where('status', 'pending')->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">In Progress</span>
                    <span class="stat-value in-progress">{{ \App\Models\Complaint::where('status', 'in_progress')->count() }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Resolved</span>
                    <span class="stat-value resolved">{{ \App\Models\Complaint::where('status', 'resolved')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <style>
        .section-description {
            text-align: center;
            color: var(--form-label);
            margin-bottom: 30px;
            font-size: 16px;
        }

        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .option-card {
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .option-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .option-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .option-card h3 {
            color: var(--form-label);
            margin-bottom: 10px;
            font-size: 20px;
        }

        .option-card p {
            color: var(--form-text);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .option-features {
            margin-bottom: 20px;
        }

        .feature-tag {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin: 2px;
        }

        .option-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .option-btn:hover {
            background: var(--primary-hover);
        }

        .quick-stats-section {
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 25px;
        }

        .quick-stats-section h3 {
            color: var(--form-label);
            margin-bottom: 20px;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
        }

        .stat-label {
            display: block;
            color: var(--form-label);
            font-size: 14px;
            margin-bottom: 5px;
        }

        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .stat-value.pending {
            color: #f39c12;
        }

        .stat-value.in-progress {
            color: #3498db;
        }

        .stat-value.resolved {
            color: #27ae60;
        }

        @media (max-width: 768px) {
            .options-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</body>
</html>
