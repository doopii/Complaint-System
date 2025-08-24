<!DOCTYPE html>
<html>
<head>
    <title>Admin - Update Complaint Status</title>
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
            <h2>Update Complaint Status</h2>
            <div class="header-actions">
                <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="back-btn">← Back to Detail</a>
            </div>
        </div>

        <!-- Current Status Summary -->
        <div class="status-summary">
            <h3>Current Status</h3>
            <div class="status-grid">
                <div class="status-item">
                    <label>Complaint ID:</label>
                    <span>#{{ $complaint->complaint_id ?? $complaint->id }}</span>
                </div>
                <div class="status-item">
                    <label>Title:</label>
                    <span>{{ $complaint->title }}</span>
                </div>
                <div class="status-item">
                    <label>Current Status:</label>
                    <span class="status-badge status-{{ $complaint->status }}">{{ ucfirst($complaint->status) }}</span>
                </div>
                <div class="status-item">
                    <label>Priority:</label>
                    <span class="priority-badge priority-{{ $complaint->priority ?? 'medium' }}">
                        {{ ucfirst($complaint->priority ?? 'medium') }}
                    </span>
                </div>
                @if(isset($complaint->assigned_to))
                <div class="status-item">
                    <label>Assigned To:</label>
                    <span>{{ $complaint->assigned_to }}</span>
                </div>
                @endif
                @if(isset($complaint->department))
                <div class="status-item">
                    <label>Department:</label>
                    <span>{{ $complaint->department }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Update Form -->
        <div class="status-update-section">
            <h3>Update Status & Resolution</h3>
            <form method="post" action="{{ route('admin.complaint.update', $complaint->id) }}" class="status-form">
                @csrf
                
                <!-- Status Selection -->
                <div class="form-group">
                    <label for="new_status">New Status:</label>
                    <select name="new_status" id="new_status" required>
                        <option value="pending" {{ ($complaint->status == 'pending') ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ ($complaint->status == 'in_progress') ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ ($complaint->status == 'resolved') ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ ($complaint->status == 'closed') ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- Resolution Details (for resolved/closed status) -->
                <div class="form-group resolution-group" id="resolution_group" style="display: none;">
                    <label for="resolution">Resolution Details:</label>
                    <textarea name="resolution" id="resolution" rows="6" placeholder="Describe how the issue was resolved, actions taken, and final outcome..."></textarea>
                </div>

                <!-- Actions Taken -->
                <div class="form-group">
                    <label for="actions_taken">Actions Taken:</label>
                    <textarea name="actions_taken" id="actions_taken" rows="4" placeholder="Describe the specific actions taken to address this complaint..."></textarea>
                </div>

                <!-- Time Spent -->
                <div class="form-group">
                    <label for="time_spent">Time Spent (hours):</label>
                    <input type="number" name="time_spent" id="time_spent" min="0" step="0.5" placeholder="Enter time spent resolving this issue">
                </div>

                <!-- Cost Information -->
                <div class="form-group">
                    <label for="cost_incurred">Cost Incurred (if any):</label>
                    <input type="number" name="cost_incurred" id="cost_incurred" min="0" step="0.01" placeholder="Enter cost if any repairs/materials were needed">
                </div>

                <!-- Follow-up Required -->
                <div class="form-group">
                    <label for="follow_up_required">Follow-up Required:</label>
                    <select name="follow_up_required" id="follow_up_required">
                        <option value="no">No Follow-up Required</option>
                        <option value="yes">Yes - Schedule Follow-up</option>
                        <option value="monitoring">Yes - Continue Monitoring</option>
                    </select>
                </div>

                <!-- Follow-up Date (if required) -->
                <div class="form-group follow-up-group" id="follow_up_group" style="display: none;">
                    <label for="follow_up_date">Follow-up Date:</label>
                    <input type="date" name="follow_up_date" id="follow_up_date">
                </div>

                <!-- Internal Notes -->
                <div class="form-group">
                    <label for="internal_notes">Internal Notes (Optional):</label>
                    <textarea name="internal_notes" id="internal_notes" rows="3" placeholder="Any internal notes or observations..."></textarea>
                </div>

                <!-- Notification Options -->
                <div class="notification-section">
                    <h4>Notification Options</h4>
                    <div class="notification-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="notify_student" value="1" checked>
                            <span>Notify student about status update</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="notify_supervisor" value="1">
                            <span>Notify supervisor about resolution</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="create_report" value="1">
                            <span>Generate resolution report</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <button type="submit" name="action" value="update" class="btn-primary">Update Status</button>
                    <button type="submit" name="action" value="save_draft" class="btn-secondary">Save as Draft</button>
                    <a href="{{ route('admin.complaint.detail', $complaint->id) }}" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Status History -->
        <div class="status-history-section">
            <h3>Status History</h3>
            <div class="history-timeline">
                @forelse($statusHistory ?? [] as $history)
                    <div class="history-item">
                        <div class="history-date">{{ $history->created_at->format('Y-m-d H:i') }}</div>
                        <div class="history-content">
                            <div class="history-status">
                                <span class="status-badge status-{{ $history->status }}">{{ ucfirst($history->status) }}</span>
                            </div>
                            @if($history->actions_taken)
                                <div class="history-actions">
                                    <strong>Actions:</strong> {{ $history->actions_taken }}
                                </div>
                            @endif
                            @if($history->resolution)
                                <div class="history-resolution">
                                    <strong>Resolution:</strong> {{ $history->resolution }}
                                </div>
                            @endif
                            <div class="history-author">
                                Updated by: {{ $history->updated_by ?? 'Admin' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="no-history">No status history available.</div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        // Show/hide resolution field based on status
        document.getElementById('new_status').addEventListener('change', function() {
            const resolutionGroup = document.getElementById('resolution_group');
            const followUpGroup = document.getElementById('follow_up_group');
            const followUpRequired = document.getElementById('follow_up_required');
            
            if (this.value === 'resolved' || this.value === 'closed') {
                resolutionGroup.style.display = 'block';
            } else {
                resolutionGroup.style.display = 'none';
            }
            
            // Show/hide follow-up date based on follow-up required
            if (followUpRequired.value !== 'no') {
                followUpGroup.style.display = 'block';
            } else {
                followUpGroup.style.display = 'none';
            }
        });

        // Show/hide follow-up date field
        document.getElementById('follow_up_required').addEventListener('change', function() {
            const followUpGroup = document.getElementById('follow_up_group');
            if (this.value !== 'no') {
                followUpGroup.style.display = 'block';
            } else {
                followUpGroup.style.display = 'none';
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('new_status');
            const resolutionGroup = document.getElementById('resolution_group');
            const followUpRequired = document.getElementById('follow_up_required');
            const followUpGroup = document.getElementById('follow_up_group');
            
            if (statusSelect.value === 'resolved' || statusSelect.value === 'closed') {
                resolutionGroup.style.display = 'block';
            }
            
            if (followUpRequired.value !== 'no') {
                followUpGroup.style.display = 'block';
            }
        });
    </script>

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

        .status-summary,
        .status-update-section,
        .status-history-section {
            margin-bottom: 30px;
            padding: 20px;
            background: var(--dashboard-alt);
            border-radius: 6px;
            border: 1px solid var(--dashboard-border);
        }

        .status-summary h3,
        .status-update-section h3,
        .status-history-section h3 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .status-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .status-item label {
            font-weight: 600;
            color: var(--form-label);
            font-size: 0.9em;
        }

        .status-item span {
            color: var(--dashboard-text);
            font-size: 1em;
        }

        .status-badge,
        .priority-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
            width: fit-content;
        }

        .status-pending { background: #6c757d; color: white; }
        .status-in_progress { background: #007bff; color: white; }
        .status-resolved { background: #28a745; color: white; }
        .status-closed { background: #6c757d; color: white; }

        .priority-low { background: #28a745; color: white; }
        .priority-medium { background: #ffc107; color: black; }
        .priority-high { background: #fd7e14; color: white; }
        .priority-urgent { background: #dc3545; color: white; }

        .status-form {
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
        .form-group input,
        .form-group textarea {
            padding: 10px 12px;
            background: var(--form-input-bg);
            border: 1px solid var(--form-input-border);
            border-radius: 4px;
            color: var(--color-text-light);
            font-family: inherit;
        }

        .form-group select:focus,
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--form-input-focus);
            outline: none;
        }

        .resolution-group,
        .follow-up-group {
            padding: 15px;
            background: var(--form-input-bg);
            border-radius: 4px;
            border: 1px solid var(--form-input-border);
        }

        .notification-section {
            padding: 15px;
            background: var(--form-input-bg);
            border-radius: 4px;
            border: 1px solid var(--form-input-border);
        }

        .notification-section h4 {
            margin-bottom: 15px;
            color: var(--form-label);
        }

        .notification-options {
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
            background: #007bff;
            color: white;
        }

        .btn-cancel {
            background: var(--button-bg);
            color: var(--button-text);
            border: 1px solid var(--form-input-border);
        }

        .btn-primary:hover { background: #218838; }
        .btn-secondary:hover { background: #0056b3; }
        .btn-cancel:hover { background: var(--button-hover-bg); }

        .history-timeline {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .history-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: var(--form-input-bg);
            border-radius: 6px;
            border: 1px solid var(--form-input-border);
        }

        .history-date {
            font-weight: 600;
            color: var(--form-label);
            font-size: 0.9em;
            min-width: 120px;
        }

        .history-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .history-status {
            margin-bottom: 5px;
        }

        .history-actions,
        .history-resolution {
            color: var(--color-text-light);
            font-size: 0.9em;
            line-height: 1.4;
        }

        .history-author {
            color: var(--color-accent-light);
            font-size: 0.8em;
            font-style: italic;
        }

        .no-history {
            color: var(--color-accent-light);
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .detail-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .history-item {
                flex-direction: column;
                gap: 10px;
            }

            .history-date {
                min-width: auto;
            }
        }
    </style>
</body>
</html> 