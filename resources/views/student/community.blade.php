<!DOCTYPE html>
<html>
<head>
    <title>Student Community - FixIt</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/community.css') }}">
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

    <div class="community-container">
        <!-- Community Header -->
        <div class="community-header">
            <h1>Student Community</h1>
            <p>we listen and we don't judge</p>
            
            <!-- Quick Stats -->
            <div class="stats-bar">
                <div class="stat-item">
                    <span class="stat-number">{{ $totalComplaints }}</span>
                    <span class="stat-label">Total Issues</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $resolvedComplaints }}</span>
                    <span class="stat-label">Resolved</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $activeComplaints }}</span>
                    <span class="stat-label">Active</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ round(($resolvedComplaints / max($totalComplaints, 1)) * 100) }}%</span>
                    <span class="stat-label">Success Rate</span>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <button class="tab-btn active" data-filter="all"><i class="fas fa-fire"></i> All Issues</button>
            <button class="tab-btn" data-filter="trending"><i class="fas fa-chart-line"></i> Trending</button>
            <button class="tab-btn" data-filter="recent"><i class="fas fa-clock"></i> Recent</button>
            <button class="tab-btn" data-filter="resolved"><i class="fas fa-check-circle"></i> Recently Resolved</button>
        </div>

        <!-- Complaint Cards Grid -->
        <div class="complaints-grid">
            @forelse($complaints as $complaint)
                <div class="complaint-card clickable-card" 
                     data-status="{{ $complaint->status }}" 
                     data-category="{{ $complaint->category }}"
                     onclick="viewComplaint('{{ $complaint->complaint_id }}')">
                    <!-- Card Header -->
                    <div class="card-header">
                        <div class="category-badge {{ strtolower(str_replace(' ', '-', $complaint->category)) }}">
                            @php
                                // Normalize category for display
                                $categoryDisplay = '';
                                $categoryIcon = '';
                                switch(strtolower($complaint->category)) {
                                    case 'facilities':
                                    case 'facility':
                                        $categoryDisplay = 'Facility';
                                        $categoryIcon = 'fas fa-building';
                                        break;
                                    case 'academic':
                                        $categoryDisplay = 'Academic';
                                        $categoryIcon = 'fas fa-book';
                                        break;
                                    case 'it':
                                    case 'technology':
                                    case 'it services':
                                        $categoryDisplay = 'Technology';
                                        $categoryIcon = 'fas fa-laptop';
                                        break;
                                    case 'security':
                                        $categoryDisplay = 'Security';
                                        $categoryIcon = 'fas fa-shield-alt';
                                        break;
                                    case 'food services':
                                    case 'food':
                                        $categoryDisplay = 'Food Services';
                                        $categoryIcon = 'fas fa-utensils';
                                        break;
                                    case 'maintenance':
                                        $categoryDisplay = 'Maintenance';
                                        $categoryIcon = 'fas fa-tools';
                                        break;
                                    default:
                                        $categoryDisplay = 'Other';
                                        $categoryIcon = 'fas fa-file-alt';
                                }
                            @endphp
                            <i class="{{ $categoryIcon }}"></i> {{ $categoryDisplay }}
                        </div>
                        <div class="timestamp">{{ $complaint->created_at->diffForHumans() }}</div>
                    </div>

                    <!-- Card Content -->
                    <div class="card-content">
                        <h3 class="complaint-title">{{ $complaint->title }}</h3>
                        <p class="complaint-description">{{ Str::limit($complaint->description, 120) }}</p>
                        
                        <!-- Badge Container - Fixed position for status and priority -->
                        <div class="badge-container">
                            <!-- Status Badge -->
                            <div class="status-badge {{ $complaint->status }}">
                                @switch($complaint->status)
                                    @case('pending')
                                        <i class="fas fa-clock"></i> Pending
                                        @break
                                    @case('in_progress')
                                        <i class="fas fa-spinner"></i> In Progress
                                        @break
                                    @case('resolved')
                                        <i class="fas fa-check-circle"></i> Resolved
                                        @break
                                    @default
                                        <i class="fas fa-file-alt"></i> {{ ucfirst($complaint->status) }}
                                @endswitch
                            </div>

                            <!-- Priority Indicator -->
                            @if($complaint->priority)
                                <div class="priority-badge priority-{{ strtolower($complaint->priority) }}" 
                                     title="{{ ucfirst($complaint->priority) }} Priority"></div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer">
                        <div class="card-actions">
                            <!-- Upvote Button -->
                            <button class="upvote-btn {{ $complaint->userHasUpvoted ? 'upvoted' : '' }}" 
                                    onclick="event.stopPropagation(); toggleUpvote('{{ $complaint->complaint_id }}', this)">
                                <span class="upvote-icon"><i class="fas fa-thumbs-up"></i></span>
                                <span class="upvote-count">{{ $complaint->upvotes }}</span>
                            </button>

                            <!-- Comments Count -->
                            <button class="comment-btn" onclick="event.stopPropagation(); viewComplaint('{{ $complaint->complaint_id }}')">
                                <span class="comment-icon"><i class="fas fa-comments"></i></span>
                                <span class="comment-count">{{ $complaint->comments_count ?? 0 }}</span>
                            </button>
                        </div>

                        <!-- Author Info -->
                        <div class="author-info">
                            <span class="author-name">
                                <i class="fas fa-graduation-cap"></i> {{ $complaint->student_name ?? 'Student' }}
                            </span>
                        </div>
                    </div>

                    <!-- Tags -->
                    @if($complaint->tags)
                        <div class="tags-container">
                            @foreach(explode(',', $complaint->tags) as $tag)
                                <span class="tag">#{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="no-complaints">
                    <div class="no-complaints-icon"><i class="icon-inbox"></i></div>
                    <h3>No Public Complaints Yet</h3>
                    <p>Be the first to share an issue with the community!</p>
                    <a href="{{ route('complaints.create') }}" class="btn">File First Complaint</a>
                </div>
            @endforelse
        </div>

        <!-- Load More Button -->
        @if($complaints->hasPages())
            <div class="load-more-container">
                {{ $complaints->links() }}
            </div>
        @endif

        <!-- Floating Action Button -->
        <div class="fab-container">
            <a href="{{ route('complaints.create') }}" class="fab">
                <span class="fab-icon"><i class="icon-plus"></i></span>
                <span class="fab-text">File New Complaint</span>
            </a>
        </div>
    </div>

    <script>
        // Tab filtering functionality
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                filterComplaints(filter);
            });
        });

        function filterComplaints(filter) {
            const cards = document.querySelectorAll('.complaint-card');
            
            cards.forEach(card => {
                const status = card.dataset.status;
                let show = true;
                
                switch(filter) {
                    case 'trending':
                        // Show cards with high upvotes 
                        const upvotes = parseInt(card.querySelector('.upvote-count').textContent);
                        show = upvotes > 5;
                        break;
                    case 'recent':
                        // Show recent cards (last 7 days)
                        show = true; 
                        break;
                    case 'resolved':
                        show = status === 'resolved';
                        break;
                    case 'all':
                    default:
                        show = true;
                        break;
                }
                
                card.style.display = show ? 'block' : 'none';
            });
        }

        // Upvote functionality
        function toggleUpvote(complaintId, button) {
            fetch(`/complaints/${complaintId}/upvote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('upvoted');
                    button.querySelector('.upvote-count').textContent = data.upvotes;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // View complaint details
        function viewComplaint(complaintId) {
            window.location.href = `/complaints/${complaintId}`;
        }
    </script>
</body>
</html>
