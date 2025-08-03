<!DOCTYPE html>
<html>
<head>
    <title>Admin - Complaint Detail</title>
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
        <div class="detail-header">
            <h2>Complaint Detail</h2>
            <div class="header-actions">
                <a href="{{ route('admin.dashboard') }}" class="back-btn">← Back to Dashboard</a>
                <a href="{{ route('admin.complaint.assign', $complaint->id) }}" class="action-btn assign-btn">Assign</a>
                <a href="{{ route('admin.complaint.update', $complaint->id) }}" class="action-btn update-btn">Update Status</a>
            </div>
        </div>

        <!-- Complaint Information -->
        <div class="complaint-info-section">
            <h3>Complaint Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Complaint ID:</label>
                    <span>#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                </div>
                <div class="info-item">
                    <label>Student ID:</label>
                    <span>{{ $complaint->student_id }}</span>
                </div>
                <div class="info-item">
                    <label>Title:</label>
                    <span>{{ $complaint->title }}</span>
                </div>
                <div class="info-item">
                    <label>Category:</label>
                    <span class="category-badge">{{ ucfirst($complaint->category) }}</span>
                </div>
                <div class="info-item">
                    <label>Priority:</label>
                    <span class="priority-badge priority-{{ $complaint->priority ?? 'medium' }}">
                        {{ ucfirst($complaint->priority ?? 'medium') }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Status:</label>
                    <span class="status-badge status-{{ $complaint->status }}">
                        {{ ucfirst($complaint->status) }}
                    </span>
                </div>
                <div class="info-item">
                    <label>Submitted:</label>
                    <span>{{ $complaint->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-item">
                    <label>Last Updated:</label>
                    <span>{{ $complaint->updated_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Complaint Description -->
        <div class="complaint-description-section">
            <h3>Description</h3>
            <div class="description-content">
                {{ $complaint->description }}
            </div>
        </div>

        <!-- Complaint Images -->
        @if($complaint->image_path)
        <div class="complaint-images-section">
            <h3>Attached Images</h3>
            <div class="image-gallery">
                <div class="image-item">
                    <img src="{{ asset('storage/' . $complaint->image_path) }}" alt="Complaint Image" class="complaint-image">
                    <div class="image-actions">
                        <a href="{{ asset('storage/' . $complaint->image_path) }}" target="_blank" class="view-full-btn">View Full Size</a>
                        <a href="{{ asset('storage/' . $complaint->image_path) }}" download class="download-btn">Download</a>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Assignment Information -->
        @if(isset($complaint->assigned_to) || isset($complaint->department))
        <div class="assignment-section">
            <h3>Assignment Information</h3>
            <div class="info-grid">
                @if(isset($complaint->assigned_to))
                <div class="info-item">
                    <label>Assigned To:</label>
                    <span>{{ $complaint->assigned_to }}</span>
                </div>
                @endif
                @if(isset($complaint->department))
                <div class="info-item">
                    <label>Department:</label>
                    <span>{{ $complaint->department }}</span>
                </div>
                @endif
                @if(isset($complaint->assigned_at))
                <div class="info-item">
                    <label>Assigned Date:</label>
                    <span>{{ $complaint->assigned_at->format('Y-m-d H:i') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Resolution Information -->
        @if(isset($complaint->resolution) || isset($complaint->resolved_at))
        <div class="resolution-section">
            <h3>Resolution Information</h3>
            <div class="info-grid">
                @if(isset($complaint->resolved_at))
                <div class="info-item">
                    <label>Resolved Date:</label>
                    <span>{{ $complaint->resolved_at->format('Y-m-d H:i') }}</span>
                </div>
                @endif
                @if(isset($complaint->resolution))
                <div class="info-item full-width">
                    <label>Resolution Details:</label>
                    <div class="resolution-content">
                        {{ $complaint->resolution }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Comments Section -->
        <div class="comments-section">
            <h3>Comments & Updates</h3>
            @forelse($comments ?? [] as $comment)
                <div class="comment-block">
                    <strong>{{ $comment->author_name ?? 'Admin' }}</strong>
                    <span class="comment-time">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                    <p>{{ $comment->content }}</p>
                </div>
            @empty
                <div class="no-comments">No comments yet.</div>
            @endforelse
        </div>

        <!-- Add Comment Form -->
        <div class="comment-section-wrapper">
            <div class="current-user-info">
                <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong> <span class="admin-badge">Admin</span>
            </div>
            <form method="post" action="{{ route('admin.complaint.addComment', $complaint->id) }}" class="comment-form">
                @csrf
                <label for="admin_comment">Add Admin Comment:</label>
                <textarea name="content" id="admin_comment" required placeholder="Enter your administrative comment or update..."></textarea>
                <button type="submit">Add Comment</button>
            </form>
        </div>
    </div>

    <style>
        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .back-btn {
            background: var(--button-bg);
            color: var(--button-text);
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            border: 1px solid var(--form-input-border);
        }

        .back-btn:hover {
            background: var(--button-hover-bg);
        }

        .complaint-info-section,
        .complaint-description-section,
        .complaint-images-section,
        .assignment-section,
        .resolution-section {
            margin-bottom: 30px;
            padding: 20px;
            background: var(--dashboard-alt);
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
        }

        .complaint-info-section h3,
        .complaint-description-section h3,
        .complaint-images-section h3,
        .assignment-section h3,
        .resolution-section h3 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-item.full-width {
            grid-column: 1 / -1;
        }

        .info-item label {
            font-weight: 600;
            color: var(--form-label);
            font-size: 0.9em;
        }

        .info-item span {
            color: var(--dashboard-text);
            font-size: 1em;
        }

        .category-badge,
        .priority-badge,
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
            width: fit-content;
        }

        .category-badge {
            background: #6c757d;
            color: white;
        }

        .priority-low { background: #28a745; color: white; }
        .priority-medium { background: #ffc107; color: black; }
        .priority-high { background: #fd7e14; color: white; }
        .priority-urgent { background: #dc3545; color: white; }

        .status-pending { background: #6c757d; color: white; }
        .status-in_progress { background: #007bff; color: white; }
        .status-resolved { background: #28a745; color: white; }
        .status-closed { background: #6c757d; color: white; }

        .description-content,
        .resolution-content {
            background: var(--form-input-bg);
            padding: 15px;
            border-radius: 4px;
            border: 1px solid var(--form-input-border);
            color: var(--color-text-light);
            white-space: pre-wrap;
            line-height: 1.5;
        }

        .image-gallery {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .image-item {
            background: var(--form-input-bg);
            padding: 15px;
            border-radius: 6px;
            border: 1px solid var(--form-input-border);
        }

        .complaint-image {
            max-width: 100%;
            max-height: 400px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .image-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .view-full-btn,
        .download-btn {
            background: var(--button-bg);
            color: var(--button-text);
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.8em;
            font-weight: 600;
            border: 1px solid var(--form-input-border);
        }

        .view-full-btn:hover,
        .download-btn:hover {
            background: var(--button-hover-bg);
        }

        .action-btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 600;
        }

        .assign-btn { background: #28a745; color: white; }
        .update-btn { background: #ffc107; color: black; }

        .action-btn:hover {
            opacity: 0.8;
        }

        .no-comments {
            color: var(--color-accent-light);
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        .current-user-info {
            background: var(--dashboard-alt);
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid var(--dashboard-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .current-user-info strong {
            color: var(--form-label);
            font-size: 0.95em;
        }

        .admin-badge {
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
        }

        .comment-form {
            margin-top: 10px;
        }

        .comment-form label {
            font-weight: 600;
            color: var(--form-label);
            margin-bottom: 8px;
            display: block;
        }

        .comment-form textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px;
            border: 1px solid var(--form-input-border);
            border-radius: 4px;
            background: var(--form-input-bg);
            color: var(--color-text-light);
            font-family: inherit;
            resize: vertical;
            margin-bottom: 15px;
        }

        .comment-form textarea:focus {
            border-color: var(--form-input-focus);
            outline: none;
        }

        .comment-form button {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .comment-form button:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html> 