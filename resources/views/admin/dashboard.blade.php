<!DOCTYPE html>
<html>
<head>
    <title>Admin Complaint Management</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <!-- TODO: Team members 3 & 4 - Add admin-specific CSS files here -->
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
                        <a href="{{ route('admin.dashboard') }}" class="active">Admin Dashboard</a>
                        <a href="{{ route('higher.management.index') }}">Higher Management</a>
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
        <h2>Admin Complaint Management</h2>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <h3>Filter Complaints</h3>
            <form method="get" action="{{ route('admin.dashboard') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="category">Category:</label>
                        <select name="category" id="category">
                            <option value="">All Categories</option>
                            <option value="facility">Facility</option>
                            <option value="academic">Academic</option>
                            <option value="security">Security</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="priority">Priority:</label>
                        <select name="priority" id="priority">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="filter-btn">Apply Filters</button>
            </form>
        </div>

        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="stat-card">
                <h4>Total Complaints</h4>
                <span class="stat-number">{{ $totalComplaints ?? 0 }}</span>
            </div>
            <div class="stat-card">
                <h4>Pending</h4>
                <span class="stat-number pending">{{ $pendingComplaints ?? 0 }}</span>
            </div>
            <div class="stat-card">
                <h4>In Progress</h4>
                <span class="stat-number in-progress">{{ $inProgressComplaints ?? 0 }}</span>
            </div>
            <div class="stat-card">
                <h4>Resolved</h4>
                <span class="stat-number resolved">{{ $resolvedComplaints ?? 0 }}</span>
            </div>
        </div>

        <!-- Complaints Table -->
        <div class="complaints-table-section">
            <h3>All Complaints</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Student ID</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints ?? [] as $complaint)
                        <tr>
                            <td>#{{ $complaint->complaint_id ?? $complaint->id }}</td>
                            <td>{{ $complaint->title }}</td>
                            <td>{{ $complaint->student_id }}</td>
                            <td>{{ ucfirst($complaint->category) }}</td>
                            <td>
                                <span class="priority-badge priority-{{ $complaint->priority ?? 'medium' }}">
                                    {{ ucfirst($complaint->priority ?? 'medium') }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $complaint->status }}">
                                    {{ ucfirst($complaint->status) }}
                                </span>
                            </td>
                            <td>{{ $complaint->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="action-btn view-btn">View</a>
                                <a href="{{ route('admin.complaint.assign', $complaint->id) }}" class="action-btn assign-btn">Assign</a>
                                <a href="{{ route('admin.complaint.update', $complaint->id) }}" class="action-btn update-btn">Update</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="no-data">No complaints found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .filter-section {
            margin-bottom: 30px;
            padding: 20px;
            background: var(--dashboard-alt);
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
        }

        .filter-section h3 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .filter-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 150px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--form-label);
            font-weight: 600;
        }

        .filter-group select {
            width: 100%;
            padding: 8px 10px;
            background: var(--form-input-bg);
            border: 1px solid var(--form-input-border);
            border-radius: 4px;
            color: var(--color-text-light);
        }

        .filter-btn {
            background: var(--button-bg);
            color: var(--button-text);
            padding: 10px 20px;
            border: 1px solid var(--form-input-border);
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            align-self: flex-start;
        }

        .filter-btn:hover {
            background: var(--button-hover-bg);
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--dashboard-alt);
            padding: 15px;
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
            text-align: center;
        }

        .stat-card h4 {
            margin: 0 0 10px 0;
            color: var(--form-label);
            font-size: 0.9em;
        }

        .stat-number {
            font-size: 1.5em;
            font-weight: 700;
            color: var(--color-text-light);
        }

        .stat-number.pending { color: #ffa500; }
        .stat-number.in-progress { color: #007bff; }
        .stat-number.resolved { color: #28a745; }

        .complaints-table-section h3 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .priority-badge, .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .priority-low { background: #28a745; color: white; }
        .priority-medium { background: #ffc107; color: black; }
        .priority-high { background: #fd7e14; color: white; }
        .priority-urgent { background: #dc3545; color: white; }

        .status-pending { background: #6c757d; color: white; }
        .status-in_progress { background: #007bff; color: white; }
        .status-resolved { background: #28a745; color: white; }
        .status-closed { background: #6c757d; color: white; }

        .action-btn {
            display: inline-block;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 0.8em;
            font-weight: 600;
        }

        .view-btn { background: #007bff; color: white; }
        .assign-btn { background: #28a745; color: white; }
        .update-btn { background: #ffc107; color: black; }

        .action-btn:hover {
            opacity: 0.8;
        }

        .user-info {
            color: var(--color-text-light);
            margin-right: 15px;
            font-weight: 600;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }

        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</body>
</html> 