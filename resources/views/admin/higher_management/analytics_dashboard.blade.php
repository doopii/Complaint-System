<!DOCTYPE html>
<html>
<head>
    <title>Analytics Dashboard - Higher Management</title>
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
            <h2>Analytics Dashboard</h2>
            <a href="{{ route('higher.management.index') }}" class="back-btn">← Back to Higher Management</a>
        </div>
        
        <!-- Key Metrics Overview -->
        <div class="metrics-overview">
            <div class="metric-card total">
                <div class="metric-icon">📊</div>
                <div class="metric-content">
                    <h3>Total Complaints</h3>
                    <span class="metric-value">{{ $totalComplaints }}</span>
                </div>
            </div>
            <div class="metric-card pending">
                <div class="metric-icon">⏳</div>
                <div class="metric-content">
                    <h3>Pending</h3>
                    <span class="metric-value">{{ $pendingComplaints }}</span>
                </div>
            </div>
            <div class="metric-card in-progress">
                <div class="metric-icon">🔄</div>
                <div class="metric-content">
                    <h3>In Progress</h3>
                    <span class="metric-value">{{ $inProgressComplaints }}</span>
                </div>
            </div>
            <div class="metric-card resolved">
                <div class="metric-icon">✅</div>
                <div class="metric-content">
                    <h3>Resolved</h3>
                    <span class="metric-value">{{ $resolvedComplaints }}</span>
                </div>
            </div>
            <div class="metric-card closed">
                <div class="metric-icon">🔒</div>
                <div class="metric-content">
                    <h3>Closed</h3>
                    <span class="metric-value">{{ $closedComplaints }}</span>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics Section -->
        <div class="analytics-grid">
            <!-- Category Distribution -->
            <div class="chart-section">
                <h3>Complaint Categories</h3>
                <div class="chart-container">
                    @if($categoryStats->count() > 0)
                        <div class="chart-data">
                            @foreach($categoryStats as $category)
                                <div class="chart-bar">
                                    <div class="bar-label">{{ ucfirst($category->category) }}</div>
                                    <div class="bar-container">
                                        <div class="bar" style="width: {{ ($category->count / $totalComplaints) * 100 }}%"></div>
                                        <span class="bar-value">{{ $category->count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">No category data available</div>
                    @endif
                </div>
            </div>

            <!-- Priority Distribution -->
            <div class="chart-section">
                <h3>Priority Levels</h3>
                <div class="chart-container">
                    @if($priorityStats->count() > 0)
                        <div class="priority-chart">
                            @foreach($priorityStats as $priority)
                                <div class="priority-item">
                                    <span class="priority-label priority-{{ $priority->priority }}">
                                        {{ ucfirst($priority->priority) }}
                                    </span>
                                    <span class="priority-count">{{ $priority->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">No priority data available</div>
                    @endif
                </div>
            </div>

            <!-- Monthly Trend -->
            <div class="chart-section full-width">
                <h3>Monthly Complaint Trends (Last 6 Months)</h3>
                <div class="chart-container">
                    @if($monthlyTrend->count() > 0)
                        <div class="trend-chart">
                            @foreach($monthlyTrend as $month)
                                <div class="trend-item">
                                    <div class="trend-month">{{ $month->month }}</div>
                                    <div class="trend-bar" style="height: {{ ($month->count / $monthlyTrend->max('count')) * 100 }}%"></div>
                                    <div class="trend-value">{{ $month->count }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-data">No trend data available</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Additional Analytics Placeholder -->
        <div class="placeholder-section">
            <h3>Advanced Analytics</h3>
            <div class="placeholder-content">
                <div class="placeholder-icon">📈</div>
                <p>Advanced analytics features will be implemented here</p>
                <div class="placeholder-features">
                    <span class="feature-item">Response Time Analysis</span>
                    <span class="feature-item">Department Performance</span>
                    <span class="feature-item">Student Satisfaction Metrics</span>
                    <span class="feature-item">Cost Analysis</span>
                </div>
            </div>
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

        .metrics-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: white;
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .metric-icon {
            font-size: 32px;
        }

        .metric-content h3 {
            margin: 0 0 5px 0;
            color: var(--form-label);
            font-size: 14px;
        }

        .metric-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .metric-card.pending .metric-value {
            color: #f39c12;
        }

        .metric-card.in-progress .metric-value {
            color: #3498db;
        }

        .metric-card.resolved .metric-value {
            color: #27ae60;
        }

        .metric-card.closed .metric-value {
            color: #95a5a6;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 40px;
        }

        .chart-section {
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 25px;
        }

        .chart-section.full-width {
            grid-column: 1 / -1;
        }

        .chart-section h3 {
            color: var(--form-label);
            margin-bottom: 20px;
            text-align: center;
        }

        .chart-container {
            min-height: 200px;
        }

        .chart-data {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .chart-bar {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .bar-label {
            min-width: 80px;
            color: var(--form-label);
            font-weight: 600;
        }

        .bar-container {
            flex: 1;
            position: relative;
            height: 30px;
            background: #f0f0f0;
            border-radius: 15px;
            overflow: hidden;
        }

        .bar {
            height: 100%;
            background: var(--primary-color);
            border-radius: 15px;
            transition: width 0.3s ease;
        }

        .bar-value {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        .priority-chart {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .priority-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: white;
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
        }

        .priority-label {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .priority-label.priority-urgent {
            background: #e74c3c;
        }

        .priority-label.priority-high {
            background: #f39c12;
        }

        .priority-label.priority-medium {
            background: #3498db;
        }

        .priority-label.priority-low {
            background: #27ae60;
        }

        .priority-count {
            font-weight: bold;
            color: var(--form-label);
        }

        .trend-chart {
            display: flex;
            align-items: end;
            justify-content: space-around;
            height: 200px;
            gap: 20px;
        }

        .trend-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .trend-month {
            font-size: 12px;
            color: var(--form-label);
            transform: rotate(-45deg);
        }

        .trend-bar {
            width: 40px;
            background: var(--primary-color);
            border-radius: 4px 4px 0 0;
            transition: height 0.3s ease;
        }

        .trend-value {
            font-size: 12px;
            color: var(--form-label);
            font-weight: 600;
        }

        .no-data {
            text-align: center;
            color: var(--form-text);
            padding: 40px;
            font-style: italic;
        }

        .placeholder-section {
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 30px;
            text-align: center;
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

        @media (max-width: 768px) {
            .analytics-grid {
                grid-template-columns: 1fr;
            }
            
            .metrics-overview {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
</body>
</html>
