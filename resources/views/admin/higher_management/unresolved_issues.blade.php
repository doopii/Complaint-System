<!DOCTYPE html>
<html>
<head>
    <title>Unresolved Issues - Higher Management</title>
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
            <h2>Unresolved Issues</h2>
            <a href="{{ route('higher.management.index') }}" class="back-btn">← Back to Higher Management</a>
        </div>
        
        <!-- Summary Overview -->
        <div class="summary-overview">
            <div class="summary-card urgent">
                <div class="summary-icon">🚨</div>
                <div class="summary-content">
                    <h3>Urgent Issues</h3>
                    <span class="summary-count">{{ $urgentIssues->count() }}</span>
                    <span class="summary-label">Requires immediate attention</span>
                </div>
            </div>
            <div class="summary-card high">
                <div class="summary-icon">⚠️</div>
                <div class="summary-content">
                    <h3>High Priority</h3>
                    <span class="summary-count">{{ $highPriorityIssues->count() }}</span>
                    <span class="summary-label">Address within 24 hours</span>
                </div>
            </div>
            <div class="summary-card medium">
                <div class="summary-icon">📋</div>
                <div class="summary-content">
                    <h3>Medium Priority</h3>
                    <span class="summary-count">{{ $mediumPriorityIssues->count() }}</span>
                    <span class="summary-label">Address within 3 days</span>
                </div>
            </div>
            <div class="summary-card low">
                <div class="summary-icon">📝</div>
                <div class="summary-content">
                    <h3>Low Priority</h3>
                    <span class="summary-count">{{ $lowPriorityIssues->count() }}</span>
                    <span class="summary-label">Address within 1 week</span>
                </div>
            </div>
        </div>

        <!-- Urgent Issues Section -->
        @if($urgentIssues->count() > 0)
        <div class="issues-section urgent-section">
            <div class="section-header">
                <h3>🚨 Urgent Issues ({{ $urgentIssues->count() }})</h3>
                <span class="priority-badge urgent">Immediate Action Required</span>
            </div>
            <div class="issues-list">
                @foreach($urgentIssues as $complaint)
                <div class="issue-card urgent">
                    <div class="issue-header">
                        <h4>{{ $complaint->title }}</h4>
                        <span class="issue-id">#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                    </div>
                    <div class="issue-details">
                        <span class="issue-category">{{ ucfirst($complaint->category) }}</span>
                        <span class="issue-student">Student: {{ $complaint->student_id }}</span>
                        <span class="issue-date">{{ $complaint->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="issue-description">
                        {{ Str::limit($complaint->description, 150) }}
                    </div>
                    <div class="issue-actions">
                        <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="action-btn view-btn">View Details</a>
                        <a href="{{ route('admin.complaint.assign', $complaint->id) }}" class="action-btn assign-btn">Assign</a>
                        <a href="{{ route('admin.complaint.update', $complaint->id) }}" class="action-btn update-btn">Update</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- High Priority Issues Section -->
        @if($highPriorityIssues->count() > 0)
        <div class="issues-section high-section">
            <div class="section-header">
                <h3>⚠️ High Priority Issues ({{ $highPriorityIssues->count() }})</h3>
                <span class="priority-badge high">24 Hour Response</span>
            </div>
            <div class="issues-list">
                @foreach($highPriorityIssues as $complaint)
                <div class="issue-card high">
                    <div class="issue-header">
                        <h4>{{ $complaint->title }}</h4>
                        <span class="issue-id">#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                    </div>
                    <div class="issue-details">
                        <span class="issue-category">{{ ucfirst($complaint->category) }}</span>
                        <span class="issue-student">Student: {{ $complaint->student_id }}</span>
                        <span class="issue-date">{{ $complaint->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="issue-description">
                        {{ Str::limit($complaint->description, 150) }}
                    </div>
                    <div class="issue-actions">
                        <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="action-btn view-btn">View Details</a>
                        <a href="{{ route('admin.complaint.assign', $complaint->id) }}" class="action-btn assign-btn">Assign</a>
                        <a href="{{ route('admin.complaint.update', $complaint->id) }}" class="action-btn update-btn">Update</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Medium Priority Issues Section -->
        @if($mediumPriorityIssues->count() > 0)
        <div class="issues-section medium-section">
            <div class="section-header">
                <h3>📋 Medium Priority Issues ({{ $mediumPriorityIssues->count() }})</h3>
                <span class="priority-badge medium">3 Day Response</span>
            </div>
            <div class="issues-list">
                @foreach($mediumPriorityIssues as $complaint)
                <div class="issue-card medium">
                    <div class="issue-header">
                        <h4>{{ $complaint->title }}</h4>
                        <span class="issue-id">#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                    </div>
                    <div class="issue-details">
                        <span class="issue-category">{{ ucfirst($complaint->category) }}</span>
                        <span class="issue-student">Student: {{ $complaint->student_id }}</span>
                        <span class="issue-date">{{ $complaint->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="issue-description">
                        {{ Str::limit($complaint->description, 150) }}
                    </div>
                    <div class="issue-actions">
                        <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="action-btn view-btn">View Details</a>
                        <a href="{{ route('admin.complaint.assign', $complaint->id) }}" class="action-btn assign-btn">Assign</a>
                        <a href="{{ route('admin.complaint.update', $complaint->id) }}" class="action-btn update-btn">Update</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Low Priority Issues Section -->
        @if($lowPriorityIssues->count() > 0)
        <div class="issues-section low-section">
            <div class="section-header">
                <h3>📝 Low Priority Issues ({{ $lowPriorityIssues->count() }})</h3>
                <span class="priority-badge low">1 Week Response</span>
            </div>
            <div class="issues-list">
                @foreach($lowPriorityIssues as $complaint)
                <div class="issue-card low">
                    <div class="issue-header">
                        <h4>{{ $complaint->title }}</h4>
                        <span class="issue-id">#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                    </div>
                    <div class="issue-details">
                        <span class="issue-category">{{ ucfirst($complaint->category) }}</span>
                        <span class="issue-student">Student: {{ $complaint->student_id }}</span>
                        <span class="issue-date">{{ $complaint->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="issue-description">
                        {{ Str::limit($complaint->description, 150) }}
                    </div>
                    <div class="issue-actions">
                        <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="action-btn view-btn">View Details</a>
                        <a href="{{ route('admin.complaint.assign', $complaint->id) }}" class="action-btn assign-btn">Assign</a>
                        <a href="{{ route('admin.complaint.update', $complaint->id) }}" class="action-btn update-btn">Update</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- No Issues Message -->
        @if($unresolvedComplaints->count() == 0)
        <div class="no-issues-section">
            <div class="no-issues-content">
                <div class="no-issues-icon">🎉</div>
                <h3>All Issues Resolved!</h3>
                <p>There are currently no unresolved complaints in the system.</p>
            </div>
        </div>
        @endif

        <!-- Advanced Management Placeholder -->
        <div class="placeholder-section">
            <h3>Advanced Issue Management</h3>
            <div class="placeholder-content">
                <div class="placeholder-icon">🔧</div>
                <p>Advanced management features will be implemented here</p>
                <div class="placeholder-features">
                    <span class="feature-item">Bulk Assignment</span>
                    <span class="feature-item">Escalation Workflows</span>
                    <span class="feature-item">Performance Metrics</span>
                    <span class="feature-item">Resource Allocation</span>
                    <span class="feature-item">Automated Routing</span>
                    <span class="feature-item">SLA Monitoring</span>
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

        .summary-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .summary-card {
            background: white;
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
        }

        .summary-card.urgent {
            border-left: 4px solid #e74c3c;
        }

        .summary-card.high {
            border-left: 4px solid #f39c12;
        }

        .summary-card.medium {
            border-left: 4px solid #3498db;
        }

        .summary-card.low {
            border-left: 4px solid #27ae60;
        }

        .summary-icon {
            font-size: 32px;
        }

        .summary-content h3 {
            margin: 0 0 5px 0;
            color: var(--form-label);
            font-size: 14px;
        }

        .summary-count {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
        }

        .summary-label {
            display: block;
            font-size: 12px;
            color: var(--form-text);
        }

        .issues-section {
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 20px;
            background: var(--dashboard-alt);
            border-radius: 8px;
            border: 1px solid var(--dashboard-border);
        }

        .section-header h3 {
            color: var(--form-label);
            margin: 0;
        }

        .priority-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .priority-badge.urgent {
            background: #e74c3c;
        }

        .priority-badge.high {
            background: #f39c12;
        }

        .priority-badge.medium {
            background: #3498db;
        }

        .priority-badge.low {
            background: #27ae60;
        }

        .issues-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .issue-card {
            background: white;
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            padding: 20px;
            transition: box-shadow 0.2s ease;
        }

        .issue-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .issue-card.urgent {
            border-left: 4px solid #e74c3c;
        }

        .issue-card.high {
            border-left: 4px solid #f39c12;
        }

        .issue-card.medium {
            border-left: 4px solid #3498db;
        }

        .issue-card.low {
            border-left: 4px solid #27ae60;
        }

        .issue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .issue-header h4 {
            color: var(--form-label);
            margin: 0;
            font-size: 16px;
        }

        .issue-id {
            color: var(--form-text);
            font-size: 12px;
            font-family: monospace;
        }

        .issue-details {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .issue-category,
        .issue-student,
        .issue-date {
            background: var(--dashboard-alt);
            color: var(--form-label);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .issue-description {
            color: var(--form-text);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .issue-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .view-btn {
            background: var(--primary-color);
            color: white;
        }

        .view-btn:hover {
            background: var(--primary-hover);
        }

        .assign-btn {
            background: #f39c12;
            color: white;
        }

        .assign-btn:hover {
            background: #e67e22;
        }

        .update-btn {
            background: #3498db;
            color: white;
        }

        .update-btn:hover {
            background: #2980b9;
        }

        .no-issues-section {
            text-align: center;
            padding: 60px 20px;
            background: var(--dashboard-alt);
            border: 1px solid var(--dashboard-border);
            border-radius: 8px;
            margin-bottom: 40px;
        }

        .no-issues-content h3 {
            color: var(--form-label);
            margin: 0 0 10px 0;
        }

        .no-issues-content p {
            color: var(--form-text);
            margin: 0;
        }

        .no-issues-icon {
            font-size: 48px;
            margin-bottom: 20px;
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
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .summary-overview {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .section-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .issue-details {
                flex-direction: column;
                gap: 8px;
            }
            
            .issue-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>
