<!DOCTYPE html>
<html>
<head>
    <title>Admin - Assign Complaint</title>
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
            <h2>Assign Complaint</h2>
            <div class="header-actions">
                <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="back-btn">← Back to Detail</a>
            </div>
        </div>

        <!-- Complaint Summary -->
        <div class="complaint-summary">
            <h3>Complaint Summary</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <label>ID:</label>
                    <span>#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                </div>
                <div class="summary-item">
                    <label>Title:</label>
                    <span>{{ $complaint->title }}</span>
                </div>
                <div class="summary-item">
                    <label>Category:</label>
                    <span class="category-badge">{{ ucfirst($complaint->category) }}</span>
                </div>
                <div class="summary-item">
                    <label>Status:</label>
                    <span class="status-badge status-{{ $complaint->status }}">{{ ucfirst($complaint->status) }}</span>
                </div>
            </div>
        </div>

        <!-- AI Analysis Section -->
        <div class="ai-analysis-section">
            <h3>AI Analysis & Recommendations</h3>
            
            <!-- Keyword Recognition -->
            <div class="analysis-block">
                <h4>Keyword Recognition</h4>
                <div class="keywords-list">
                    @foreach($detectedKeywords ?? ['facility', 'broken', 'repair'] as $keyword)
                        <span class="keyword-tag">{{ $keyword }}</span>
                    @endforeach
                </div>
                <p class="analysis-note">Keywords detected: {{ implode(', ', $detectedKeywords ?? ['facility', 'broken', 'repair']) }}</p>
            </div>

            <!-- Category Matching -->
            <div class="analysis-block">
                <h4>Category Matching</h4>
                <div class="category-match">
                    <div class="match-item">
                        <span class="match-label">Detected Category:</span>
                        <span class="match-value">{{ $detectedCategory ?? 'Facility' }}</span>
                        <span class="confidence">Confidence: {{ $confidence ?? '85%' }}</span>
                    </div>
                </div>
            </div>

            <!-- Duplicate Detection -->
            <div class="analysis-block">
                <h4>Duplicate Detection</h4>
                @if(isset($duplicateComplaints) && count($duplicateComplaints) > 0)
                    <div class="duplicate-warning">
                        <span class="warning-icon">⚠️</span>
                        <span>Potential duplicate complaints found!</span>
                    </div>
                    <div class="duplicate-list">
                        @foreach($duplicateComplaints ?? [] as $duplicate)
                            <div class="duplicate-item">
                                <span>ID: #{{ $duplicate->id }}</span>
                                <span>{{ $duplicate->title }}</span>
                                <span class="similarity">{{ $duplicate->similarity ?? '85%' }} similar</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-duplicate">
                        <span class="check-icon">✅</span>
                        <span>No duplicate complaints detected</span>
                    </div>
                @endif
            </div>

            <!-- Priority Assessment -->
            <div class="analysis-block">
                <h4>Priority Assessment</h4>
                <div class="priority-assessment">
                    <div class="priority-item">
                        <span class="priority-label">Recommended Priority:</span>
                        <span class="priority-value priority-{{ $recommendedPriority ?? 'medium' }}">
                            {{ ucfirst($recommendedPriority ?? 'medium') }}
                        </span>
                    </div>
                    <div class="priority-reason">
                        <span class="reason-label">Reason:</span>
                        <span class="reason-text">{{ $priorityReason ?? 'Based on keyword analysis and category matching' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Form -->
        <div class="assignment-form-section">
            <h3>Manual Assignment</h3>
            <form method="post" action="{{ route('admin.complaint.assign', $complaint->id) }}" class="assignment-form">
                @csrf
                
                <!-- Department Assignment -->
                <div class="form-group">
                    <label for="department">Assign to Department:</label>
                    <select name="department" id="department" required>
                        <option value="">Select Department</option>
                        <option value="facilities" {{ ($complaint->category == 'facility') ? 'selected' : '' }}>Facilities Management</option>
                        <option value="academic">Academic Affairs</option>
                        <option value="security">Campus Security</option>
                        <option value="it">IT Services</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="student_services">Student Services</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Staff Assignment -->
                <div class="form-group">
                    <label for="assigned_to">Assign to Staff Member:</label>
                    <select name="assigned_to" id="assigned_to" required>
                        <option value="">Select Staff Member</option>
                        <option value="john_doe">John Doe - Facilities Manager</option>
                        <option value="jane_smith">Jane Smith - Academic Coordinator</option>
                        <option value="mike_wilson">Mike Wilson - Security Officer</option>
                        <option value="sarah_jones">Sarah Jones - IT Support</option>
                        <option value="david_brown">David Brown - Maintenance</option>
                    </select>
                </div>

                <!-- Priority Override -->
                <div class="form-group">
                    <label for="priority">Priority Level:</label>
                    <select name="priority" id="priority" required>
                        <option value="low" {{ ($complaint->priority == 'low') ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ ($complaint->priority == 'medium' || !$complaint->priority) ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ ($complaint->priority == 'high') ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ ($complaint->priority == 'urgent') ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Status Update -->
                <div class="form-group">
                    <label for="status">Update Status:</label>
                    <select name="status" id="status" required>
                        <option value="pending" {{ ($complaint->status == 'pending') ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ ($complaint->status == 'in_progress') ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ ($complaint->status == 'resolved') ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ ($complaint->status == 'closed') ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- Assignment Notes -->
                <div class="form-group">
                    <label for="assignment_notes">Assignment Notes:</label>
                    <textarea name="assignment_notes" id="assignment_notes" rows="4" placeholder="Enter any additional notes for the assignment..."></textarea>
                </div>

                <!-- Auto-assignment Options -->
                <div class="auto-assignment-section">
                    <h4>Auto-assignment Options</h4>
                    <div class="auto-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="auto_assign" value="1" checked>
                            <span>Use AI recommendations for automatic assignment</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="notify_staff" value="1" checked>
                            <span>Send notification to assigned staff member</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="escalate_urgent" value="1">
                            <span>Escalate urgent complaints to supervisor</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" name="action" value="assign" class="btn-primary">Assign Complaint</button>
                    <button type="submit" name="action" value="reject" class="btn-secondary">Reject as Invalid</button>
                    <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="btn-cancel">Cancel</a>
                </div>
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

        .complaint-summary,
        .ai-analysis-section,
        .assignment-form-section {
            margin-bottom: 30px;
            padding: 20px;
            background: var(--dashboard-alt);
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
        }

        .complaint-summary h3,
        .ai-analysis-section h3,
        .assignment-form-section h3 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .summary-item label {
            font-weight: 600;
            color: var(--form-label);
            font-size: 0.9em;
        }

        .summary-item span {
            color: var(--dashboard-text);
            font-size: 1em;
        }

        .category-badge,
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

        .status-pending { background: #6c757d; color: white; }
        .status-in_progress { background: #007bff; color: white; }
        .status-resolved { background: #28a745; color: white; }
        .status-closed { background: #6c757d; color: white; }

        .analysis-block {
            margin-bottom: 20px;
            padding: 15px;
            background: var(--form-input-bg);
            border-radius: 4px;
            border: 1px solid var(--form-input-border);
        }

        .analysis-block h4 {
            margin-bottom: 10px;
            color: var(--form-label);
            font-size: 1em;
        }

        .keywords-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 10px;
        }

        .keyword-tag {
            background: var(--mc-purple);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .analysis-note {
            color: var(--color-accent-light);
            font-size: 0.9em;
            margin: 0;
        }

        .category-match,
        .priority-assessment {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .match-item,
        .priority-item {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .match-label,
        .priority-label,
        .reason-label {
            font-weight: 600;
            color: var(--form-label);
            font-size: 0.9em;
        }

        .match-value,
        .priority-value {
            font-weight: 600;
            color: var(--color-text-light);
        }

        .confidence {
            color: var(--color-accent-light);
            font-size: 0.8em;
        }

        .priority-low { color: #28a745; }
        .priority-medium { color: #ffc107; }
        .priority-high { color: #fd7e14; }
        .priority-urgent { color: #dc3545; }

        .duplicate-warning,
        .no-duplicate {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }

        .duplicate-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .no-duplicate {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .duplicate-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .duplicate-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            background: var(--form-input-bg);
            border-radius: 4px;
            font-size: 0.9em;
            flex-wrap: wrap;
            gap: 10px;
        }

        .similarity {
            color: var(--color-accent-light);
            font-size: 0.8em;
        }

        .priority-reason {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .reason-text {
            color: var(--color-accent-light);
            font-size: 0.9em;
            line-height: 1.4;
        }

        .assignment-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--form-label);
        }

        .form-group select,
        .form-group textarea {
            padding: 10px 12px;
            background: var(--form-input-bg);
            border: 1px solid var(--form-input-border);
            border-radius: 4px;
            color: var(--color-text-light);
            font-family: inherit;
        }

        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--form-input-focus);
            outline: none;
        }

        .auto-assignment-section {
            padding: 15px;
            background: var(--form-input-bg);
            border-radius: 4px;
            border: 1px solid var(--form-input-border);
        }

        .auto-assignment-section h4 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .auto-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            color: var(--color-text-light);
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin: 0;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-primary,
        .btn-secondary,
        .btn-cancel {
            padding: 12px 24px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-size: 1em;
        }

        .btn-primary {
            background: #28a745;
            color: white;
        }

        .btn-secondary {
            background: #dc3545;
            color: white;
        }

        .btn-cancel {
            background: var(--button-bg);
            color: var(--button-text);
            border: 1px solid var(--form-input-border);
        }

        .btn-primary:hover { background: #218838; }
        .btn-secondary:hover { background: #c82333; }
        .btn-cancel:hover { background: var(--button-hover-bg); }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .duplicate-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</body>
</html> 