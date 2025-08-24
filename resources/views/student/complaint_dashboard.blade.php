<!DOCTYPE html>
<html>
<head>
    <title>My Complaints Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/dashboard.css') }}">
</head>
<body>
    <!-- Site Header -->
    <header class="navbar">
        <div class="navbar-inner">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="logo">FixIt</a>
                <nav class="navbar-center">
                    <a href="{{ route('home') }}">Home</a>
                    @auth
                        @if(auth()->user()->isStudent())
                            <a href="{{ route('student.community') }}">Community</a>
                            <a href="{{ route('complaints.dashboard') }}">My Complaints</a>
                            <a href="{{ route('student.profile') }}">Profile</a>
                        @elseif(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        @endif
                    @else
                        <a href="{{ route('complaints.dashboard', ['student_id' => $studentId ?? '']) }}" class="active">Dashboard</a>
                        <a href="{{ route('complaints.create', ['student_id' => $studentId ?? '']) }}">Submit Complaint</a>
                    @endauth
                </nav>
            </div>
            <div class="navbar-right">
                @auth
                    <span class="user-info">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
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
        <h2>My Complaints</h2>
        
        <!-- Success message -->
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        @auth
            @if(auth()->user()->isStudent())
                <!-- Authenticated student - no need to ask for student ID -->
                <p>Student ID: <strong>{{ auth()->user()->student_id }}</strong></p>
            @endif
        @else
            <!-- Ask for student ID if not authenticated and not set -->
            @if(empty($studentId))
                <form method="get" action="{{ route('complaints.dashboard') }}">
                    <label for="student_id">Enter Student ID:</label>
                    <input type="text" name="student_id" id="student_id" required>
                    <button type="submit">Go</button>
                </form>
                @php return; @endphp
            @endif
        @endauth

        <!-- Link to file new complaint -->
        <div class="add-complaint">
            @auth
                <a href="{{ route('complaints.create') }}">+ File New Complaint</a>
            @else
                <a href="{{ route('complaints.create') }}?student_id={{ $studentId }}">+ File New Complaint</a>
            @endauth
        </div>

        <!-- Filter Section -->
        @if(!empty($studentId) || auth()->check())
            <div class="filter-container">
                <h3>Filter Complaints</h3>
                <form method="GET" action="{{ route('complaints.dashboard') }}" class="filter-form">
                    @if(!auth()->check() && !empty($studentId))
                        <input type="hidden" name="student_id" value="{{ $studentId }}">
                    @endif
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status">Status:</label>
                            <select name="status" id="status">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="category">Category:</label>
                            <select name="category" id="category">
                                <option value="">All Categories</option>
                                <option value="Academic" {{ request('category') == 'Academic' ? 'selected' : '' }}>Academic</option>
                                <option value="Facilities" {{ request('category') == 'Facilities' ? 'selected' : '' }}>Facilities</option>
                                <option value="IT" {{ request('category') == 'IT' ? 'selected' : '' }}>IT Services</option>
                                <option value="Security" {{ request('category') == 'Security' ? 'selected' : '' }}>Security</option>
                                <option value="Food Services" {{ request('category') == 'Food Services' ? 'selected' : '' }}>Food Services</option>
                                <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="priority">Priority:</label>
                            <select name="priority" id="priority">
                                <option value="">All Priorities</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="date_from">From Date:</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="filter-group">
                            <label for="date_to">To Date:</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">Apply Filters</button>
                        <a href="{{ route('complaints.dashboard') }}{{ !auth()->check() && !empty($studentId) ? '?student_id=' . $studentId : '' }}" class="btn-clear">Reset</a>
                    </div>
                </form>
            </div>
        @endif

        <!-- List of complaints (only show if student ID is set) -->
        @if(!empty($studentId) || auth()->check())
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $complaint)
                        <tr class="clickable-row" data-href="/complaints/{{ $complaint->complaint_id }}{{ auth()->check() ? '' : '?student_id=' . $studentId }}">
                            <td>{{ $complaint->title }}</td>
                            <td>{{ ucfirst($complaint->category) }}</td>
                            <td>{{ ucfirst($complaint->status) }}</td>
                            <td>{{ $complaint->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="no-data">No complaints found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
    </div>

    <!-- Clickable row script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.clickable-row').forEach(function(row) {
            row.addEventListener('click', function() {
                window.location = this.dataset.href;
            });
            row.style.cursor = 'pointer';
        });
    });
    </script>
</body>
</html>
