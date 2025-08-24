<!DOCTYPE html>
<html>
<head>
    <title>Complaint Details</title>
    <link rel="stylesheet" href="{{ asset('css/shared/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/student-shared.css') }}">
    <link rel="stylesheet" href="{{ asset('css/student/complaint-detail.css') }}">
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

    <!-- Status Timeline Component -->
    <div class="form-container">
        <!-- Timeline bar without container box -->
        <div class="timeline-header">
            <h3 class="timeline-title">Status Progress</h3>
        </div>
        <div class="timeline-container">
            <!-- Progress line -->
            <div class="timeline-line">
                <div class="timeline-progress" style="width: {{ $progressWidth ?? 33 }}%;"></div>
            </div>
            <!-- Step 1: Pending -->
            <div class="timeline-step">
                <div class="step-circle {{ $complaint->status == 'pending' ? 'current' : 'completed' }}">
                    {{ $complaint->status == 'pending' ? '' : '✓' }}
                </div>
                <div class="step-label {{ $complaint->status == 'pending' ? 'current' : 'completed' }}">Pending</div>
                <div class="step-date">{{ $complaint->created_at->format('M d, Y') }}</div>
            </div>
            <!-- Step 2: In Progress -->
            <div class="timeline-step">
                <div class="step-circle {{ $complaint->status == 'in_progress' ? 'current' : (($progressWidth ?? 33) >= 50 ? 'completed' : '') }}">
                    @if($complaint->status == 'in_progress')
                    @elseif(($progressWidth ?? 33) >= 50)
                        ✓
                    @else
                    @endif
                </div>
                <div class="step-label {{ $complaint->status == 'in_progress' ? 'current' : (($progressWidth ?? 33) >= 50 ? 'completed' : '') }}">In Progress</div>
                <div class="step-date">
                    @if($complaint->status == 'in_progress' || $complaint->status == 'resolved')
                        {{ $complaint->updated_at->format('M d, Y') }}
                    @endif
                </div>
            </div>
            <!-- Step 3: Resolved -->
            <div class="timeline-step">
                <div class="step-circle {{ $complaint->status == 'resolved' ? 'completed' : '' }}">
                    {{ $complaint->status == 'resolved' ? '✓' : '' }}
                </div>
                <div class="step-label {{ $complaint->status == 'resolved' ? 'completed' : '' }}">Resolved</div>
                <div class="step-date">
                    @if($complaint->status == 'resolved')
                        {{ $complaint->resolved_at ? $complaint->resolved_at->format('M d, Y') : $complaint->updated_at->format('M d, Y') }}
                    @endif
                </div>
            </div>
        </div>

        <h2>Complaint Details</h2>
        <div class="details-block">
            <div class="details-row">
                <span class="details-label">Complaint ID</span>
                <span class="details-value">{{ $complaint->complaint_id }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Student ID</span>
                <span class="details-value">{{ $complaint->student_id }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Title</span>
                <span class="details-value">{{ $complaint->title }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Description</span>
                <span class="details-value">{{ $complaint->description }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Category</span>
                <span class="details-value">{{ $complaint->category }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Status</span>
                <span class="details-value">{{ $complaint->status }}</span>
            </div>
            <div class="details-row">
                <span class="details-label">Created At</span>
                <span class="details-value">{{ $complaint->created_at }}</span>
            </div>
        </div>
        @if($complaint->photo)
        <div class="details-photo">
            <span class="details-label">Photo</span>
            <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint Photo">
        </div>
        @endif
        <div class="form-footer">
            <a href="{{ route('complaints.dashboard', ['student_id' => $studentId]) }}">&larr; Back to Dashboard</a>
        </div>
    </div>
    
    <div class="comment-section-wrapper">
        <!-- Comment Form -->
        <div class="comment-form">
            @auth
                <form method="POST" action="{{ route('complaints.addComment', $complaint->complaint_id) }}">
                    @csrf
                    <label for="comment_text">Add Comment</label>
                    <textarea name="comment_text" id="comment_text" required maxlength="1000" placeholder="Write your comment here..."></textarea>
                    <button type="submit">Add Comment</button>
                </form>
            @else
                <div class="login-prompt">
                    <p>Please <a href="{{ route('login') }}">login</a> to add a comment.</p>
                </div>
            @endauth
        </div>

        <h3 class="details-label" style="padding-top: 20px;">Comments</h3>
        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        @if($comments->isEmpty())
            <p class="no-comments">No comments yet.</p>
        @else
            <div class="comments-list">
                @foreach($comments as $comment)
                    <div class="comment-block">
                        <div class="comment-header">
                            <strong>{{ $comment->username }}</strong>
                            <span class="comment-time">{{ $comment->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="comment-content">
                            <p>{{ !empty($comment->comment_text) ? $comment->comment_text : 'No comment text available' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
    document.querySelector('.comment-form form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = e.target;
        const url = form.action;
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                alert('Error: ' + JSON.stringify(errorData.errors));
                return;
            }

            const newComment = await response.json();

            // Remove "No comments yet." if present
            const noComments = document.querySelector('.no-comments');
            if (noComments) noComments.remove();

            // Append new comment
            let commentList = document.querySelector('.comments-list');
            if (!commentList) {
                // Create the comments list and place it after the "Comments" heading
                commentList = document.createElement('div');
                commentList.classList.add('comments-list');
                
                // Find the "Comments" heading and insert the list after it
                const commentsHeading = document.querySelector('h3.details-label');
                const noCommentsMessage = document.querySelector('.no-comments');
                
                if (noCommentsMessage) {
                    // Replace "No comments yet." message with the comments list
                    noCommentsMessage.parentNode.replaceChild(commentList, noCommentsMessage);
                } else if (commentsHeading) {
                    // Insert after the comments heading
                    commentsHeading.parentNode.insertBefore(commentList, commentsHeading.nextSibling);
                }
            }

            const div = document.createElement('div');
            div.classList.add('comment-block');
            div.innerHTML = `
                <div class="comment-header">
                    <strong>${newComment.username}</strong>
                    <span class="comment-time">${newComment.created_at}</span>
                </div>
                <div class="comment-content">
                    <p>${newComment.comment.comment_text}</p>
                </div>
            `;
            commentList.prepend(div);

            form.reset();

        } catch (error) {
            alert('Failed to submit comment.');
        }
    });
    </script>
</body>
</html>