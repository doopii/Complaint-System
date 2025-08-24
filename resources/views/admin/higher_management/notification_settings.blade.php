<!DOCTYPE html>
<html>
<head>
    <title>Notification Settings - Higher Management</title>
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
        <div class="page-header">
            <h2>Notification Settings</h2>
            <a href="{{ route('higher.management.index') }}" class="back-btn">← Back to Higher Management</a>
        </div>
        
        <!-- Notification Settings Overview -->
        <div class="settings-overview">
            <div class="overview-card">
                <div class="overview-icon">🔔</div>
                <div class="overview-content">
                    <h3>System Notifications</h3>
                    <p>Configure how and when you receive notifications about system events, complaints, and updates.</p>
                </div>
            </div>
        </div>

        <!-- Notification Categories -->
        <div class="notification-categories">
            <h3>Notification Categories</h3>
            
            <!-- Urgent Issues -->
            <div class="category-section">
                <div class="category-header">
                    <h4>🚨 Urgent Issues</h4>
                    <span class="category-description">High priority complaints and system alerts</span>
                </div>
                <div class="category-settings">
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked disabled>
                            <span class="checkmark"></span>
                            Email Notifications
                        </label>
                        <span class="setting-note">Always enabled for urgent issues</span>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked disabled>
                            <span class="checkmark"></span>
                            SMS Alerts
                        </label>
                        <span class="setting-note">Always enabled for urgent issues</span>
                    </div>
                </div>
            </div>

            <!-- New Complaints -->
            <div class="category-section">
                <div class="category-header">
                    <h4>📝 New Complaints</h4>
                    <span class="category-description">Notifications when new complaints are submitted</span>
                </div>
                <div class="category-settings">
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked>
                            <span class="checkmark"></span>
                            Email Notifications
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox">
                            <span class="checkmark"></span>
                            SMS Alerts
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked>
                            <span class="checkmark"></span>
                            Dashboard Alerts
                        </label>
                    </div>
                </div>
            </div>

            <!-- Status Updates -->
            <div class="category-section">
                <div class="category-header">
                    <h4>🔄 Status Updates</h4>
                    <span class="category-description">Updates on complaint status changes</span>
                </div>
                <div class="category-settings">
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked>
                            <span class="checkmark"></span>
                            Email Notifications
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox">
                            <span class="checkmark"></span>
                            SMS Alerts
                        </label>
                    </div>
                </div>
            </div>

            <!-- System Reports -->
            <div class="category-section">
                <div class="category-header">
                    <h4>📊 System Reports</h4>
                    <span class="category-description">Weekly and monthly performance reports</span>
                </div>
                <div class="category-settings">
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked>
                            <span class="checkmark"></span>
                            Weekly Summary
                        </label>
                    </div>
                    <div class="setting-item">
                        <label class="setting-label">
                            <input type="checkbox" checked>
                            <span class="checkmark"></span>
                            Monthly Report
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Settings Placeholder -->
        <div class="placeholder-section">
            <h3>Advanced Notification Settings</h3>
            <div class="placeholder-content">
                <div class="placeholder-icon">⚙️</div>
                <p>Advanced notification configuration options will be implemented here</p>
                <div class="placeholder-features">
                    <span class="feature-item">Custom Notification Rules</span>
                    <span class="feature-item">Escalation Policies</span>
                    <span class="feature-item">Time-based Notifications</span>
                    <span class="feature-item">Department-specific Settings</span>
                    <span class="feature-item">Notification Templates</span>
                    <span class="feature-item">Delivery Preferences</span>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="action-section">
            <button type="button" class="save-btn">Save Notification Settings</button>
        </div>
    </div>

    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .back-btn {
            background: var(--dashboard-alt);
            color: var(--form-label);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: 1px solid var(--dashboard-border);
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: var(--dashboard-border);
        }

        .settings-overview {
            margin-bottom: 30px;
        }

        .overview-card {
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .overview-icon {
            font-size: 48px;
        }

        .overview-content h3 {
            color: var(--form-label);
            margin: 0 0 10px 0;
        }

        .overview-content p {
            color: var(--form-text);
            margin: 0;
            line-height: 1.5;
        }

        .notification-categories {
            margin-bottom: 40px;
        }

        .notification-categories h3 {
            color: var(--form-label);
            margin-bottom: 25px;
            text-align: center;
        }

        .category-section {
            background: white;
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .category-header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--dashboard-border);
        }

        .category-header h4 {
            color: var(--form-label);
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        .category-description {
            color: var(--form-text);
            font-size: 14px;
        }

        .category-settings {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .setting-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: var(--form-label);
            font-weight: 500;
        }

        .setting-label input[type="checkbox"] {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid var(--dashboard-border);
            border-radius: 4px;
            position: relative;
            transition: all 0.2s ease;
        }

        .setting-label input[type="checkbox"]:checked + .checkmark {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .setting-label input[type="checkbox"]:checked + .checkmark::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .setting-label input[type="checkbox"]:disabled + .checkmark {
            background: #f0f0f0;
            border-color: #ccc;
            cursor: not-allowed;
        }

        .setting-note {
            color: var(--form-text);
            font-size: 12px;
            font-style: italic;
        }

        .placeholder-section {
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }

        .placeholder-section h3 {
            color: var(--form-label);
            margin-bottom: 20px;
        }

        .placeholder-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .placeholder-icon {
            font-size: 48px;
            opacity: 0.6;
        }

        .placeholder-content p {
            color: var(--form-text);
            margin: 0;
        }

        .placeholder-features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .feature-item {
            background: white;
            color: var(--form-label);
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            border: 1px solid var(--dashboard-border);
        }

        .action-section {
            text-align: center;
        }

        .save-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .save-btn:hover {
            background: var(--primary-hover);
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .overview-card {
                flex-direction: column;
                text-align: center;
            }
            
            .setting-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</body>
</html>
