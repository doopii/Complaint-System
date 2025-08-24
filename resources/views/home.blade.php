<!DOCTYPE html>
<html>
<head>
    <title>FixIt</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/profile.css') }}">
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
        {{-- Hero --}}
        <section class="welcome-section">
            <h1>Welcome to FixIt, {{ Auth::user()->name }}</h1>
            <p>Got a problem? File it here. Admin loves reading the 100th “Wi-Fi is down” complaint.</p>
            <div class="action-cards">
                <a href="{{ route('student.profile') }}" class="action-card profile-card">
                    <h3>My Profile</h3>
                    <p>View and edit your profile information.</p>
                </a>
                <a href="{{ route('complaints.create') }}" class="action-card create-card">
                    <h3>File New Complaint</h3>
                    <p>Add to the endless list of broken stuff.</p>
                </a>
                <a href="{{ route('complaints.dashboard') }}" class="action-card student-card">
                    <h3>My Complaints</h3>
                    <p>Check if anyone cared yet.</p>
                </a>
                <a href="{{ route('student.community') }}" class="action-card community-card">
                    <h3>Community</h3>
                    <p>See what everyone else is complaining about.</p>
                </a>
            </div>
        </section>

        {{-- My Status Overview --}}
        <section class="dashboard-cards" style="margin-top:24px;">
            <h2>My Status</h2>
            <div class="stats-bar">
                <div class="stat-item">
                    <span class="stat-number">{{ $counts['open'] ?? 0 }}</span>
                    <span class="stat-label">Open</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $counts['in_progress'] ?? 0 }}</span>
                    <span class="stat-label">In Progress</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $counts['resolved_30d'] ?? 0 }}</span>
                    <span class="stat-label">Resolved (30 days)</span>
                </div>
            </div>
        </section>

        {{-- Recent Activity (your items only) --}}
        <section style="margin-top:24px;">
            <h2>Recent Activity</h2>

            @if(!empty($recentActivities))
                <ul class="activity-list">
                    @foreach($recentActivities as $a)
                        <li>
                            <a href="{{ route('complaints.show', $a->complaint_id) }}">
                                {{ Str::limit($a->title, 60) }}
                            </a>
                            <span class="activity-meta">— {{ $a->description }} • {{ $a->created_at->diffForHumans() }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="form-footer" style="margin-top:10px;">
                    <a href="{{ route('complaints.dashboard') }}">View all</a>
                </div>
            @else
                <div class="no-data">No recent activity yet.</div>
            @endif
        </section>

        {{-- Trending in Community --}}
        <section style="margin-top:24px;">
            <h2>Trending in Community</h2>
            @if(!empty($trending))
                <div class="complaints-grid">
                    @foreach($trending as $c)
                        <a class="complaint-card clickable-card" href="{{ route('complaints.show', $c->id) }}">
                            <div class="card-header">
                                <span class="category-badge {{ strtolower(str_replace(' ', '-', $c->category)) }}">
                                    {{ $c->category }}
                                </span>
                                <span class="timestamp">{{ $c->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="card-content">
                                <div class="complaint-title">{{ $c->title }}</div>
                                <div class="complaint-description">{{ Str::limit($c->description, 120) }}</div>
                                <span class="status-badge {{ $c->status }}">{{ ucfirst(str_replace('_',' ',$c->status)) }}</span>
                            </div>
                            <div class="card-footer">
                                <div class="author-info">by {{ $c->student_name }}</div>
                                <div class="card-actions">
                                    <span>👍 {{ $c->upvotes }}</span>
                                    <span>💬 {{ $c->comments_count }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="form-footer">
                    <a href="{{ route('student.community') }}">View community</a>
                </div>
            @else
                <div class="no-data">No trending items this week.</div>
            @endif
        </section>

        {{-- Help & Policies --}}
        <section style="margin-top:24px;">
            <h2>Help & Policies</h2>
            <div class="help-grid">
                <div class="help-card">
                    <strong>Write a clear complaint</strong>
                    <p>“It’s broken” isn’t helpful. Location, time, details — and yes, photos exist for a reason.</p>
                </div>
                <div class="help-card">
                    <strong>Categories & priority</strong>
                    <p>Facility, Academic, Tech, Service, Other. Emergencies cut the line. Minor stuff waits.</p>
                </div>
                <div class="help-card">
                    <strong>Response time</strong>
                    <p>If it’s urgent, fast. If not, a day or two. We’re good, not magical.</p>
                </div>
                <div class="help-card">
                    <strong>Need help?</strong>
                    <p>
                        Email Student Affairs at 
                        <a href="mailto:fixit@gmail.com
                            ?subject=FixIt%20Complaint%20Support
                            &body=Hello%20Student%20Affairs,%0A%0AI%20would%20like%20to%20ask%20about%20my%20complaint.%0A%0AStudent%20Name:%20[Your%20Name]%0AStudent%20ID:%20[Your%20ID]%0AComplaint%20ID:%20[Your%20Complaint%20ID]%0AIssue%20Details:%20[Describe%20your%20issue%20here]%0A%0AThank%20you.">
                            fixit@gmail.com
                        </a>.
                        Don’t worry, someone will actually read it.
                    </p>
                </div>
            </div>
        </section>
    </div>

</body>
</html>
