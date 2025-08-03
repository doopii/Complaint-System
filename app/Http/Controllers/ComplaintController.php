<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Comment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ComplaintController extends Controller
{

    public function index(Request $request)
    {
        // Redirect to dashboard for authenticated users
        if (Auth::check() && Auth::user()->isStudent()) {
            return redirect()->route('complaints.dashboard');
        }
        
        // For backward compatibility with old URLs
        $studentId = $request->query('student_id');
        if ($studentId) {
            return redirect()->route('complaints.dashboard', ['student_id' => $studentId]);
        }
        
        return redirect()->route('login');
    }

    // Show all complaints for a student (dashboard)
    public function dashboard(Request $request)
    {
        // Check if user is authenticated
        if (Auth::check() && Auth::user()->isStudent()) {
            $user = Auth::user();
            $studentId = $user->student_id;
            
            $query = Complaint::where('student_id', $studentId);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $complaints = $query->orderBy('created_at', 'desc')->get();
                
            return view('student.complaint_dashboard', compact('complaints', 'studentId'));
        }
        
        // For backward compatibility with old URLs
        $studentId = $request->query('student_id'); 
        $complaints = [];
        if ($studentId) {
            $query = Complaint::where('student_id', $studentId);
            
            // Apply filters for backward compatibility
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }
            
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $complaints = $query->orderBy('created_at', 'desc')->get();
        }
        return view('student.complaint_dashboard', compact('complaints', 'studentId'));
    }

    // Show form to create a new complaint
    public function create(Request $request)
    {
        // Check if user is authenticated
        if (Auth::check() && Auth::user()->isStudent()) {
            $user = Auth::user();
            $studentId = $user->student_id;
            return view('student.complaint_create', compact('studentId'));
        }
        
        // For backward compatibility with old URLs
        $studentId = $request->query('student_id');
        return view('student.complaint_create', compact('studentId'));
    }

    // Store a newly created complaint in the database
    public function store(Request $request)
    {
        // Get student_id from authenticated user or form input
        $studentId = null;
        if (Auth::check() && Auth::user()->isStudent()) {
            $studentId = Auth::user()->student_id;
        } else {
            // For backward compatibility
            $validated = $request->validate([
                'student_id' => 'required|string|max:20',
            ]);
            $studentId = $validated['student_id'];
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaint_photos', 'public');
        }

        Complaint::create([
            'student_id' => $studentId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'photo' => $photoPath,
            'status' => 'pending'
        ]);

        // Redirect based on authentication status
        if (Auth::check()) {
            return redirect()->route('complaints.dashboard')
                ->with('success', 'Complaint submitted successfully!');
        } else {
            // For backward compatibility
            return redirect()->route('complaints.dashboard', ['student_id' => $studentId])
                ->with('success', 'Complaint submitted successfully!');
        }
    }

    // Show a single complaint by ID (DETAIL)
    public function show(Request $request, $complaint)
    {
        // Get student_id from authenticated user or query param
        $studentId = null;
        if (Auth::check() && Auth::user()->isStudent()) {
            $studentId = Auth::user()->student_id;
        } else {
            $studentId = $request->query('student_id');
        }

        // Find complaint for this student ID
        $complaint = Complaint::where('student_id', $studentId)->findOrFail($complaint);

        // Get comments for this complaint
        $comments = $complaint->comments()->orderBy('created_at', 'asc')->get();

        return view('student.complaint_detail', compact('complaint', 'studentId', 'comments'));
    }

    public function addComment(Request $request, $complaintId)
    {
        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        // Get user information from authentication
        $user = Auth::user();
        $username = 'Anonymous';
        $userType = 'guest';
        $userId = null;

        if ($user) {
            $userId = $user->id;
            if ($user->isStudent()) {
                $username = $user->student ? $user->student->name : $user->email;
                $userType = 'student';
            } elseif ($user->isAdmin()) {
                $username = $user->admin ? $user->admin->name : $user->email;
                $userType = 'admin';
            } else {
                $username = $user->email;
                $userType = 'user';
            }
        }

        $comment = Comment::create([
            'comment_id' => \Illuminate\Support\Str::uuid()->toString(),
            'complaint_id' => $complaintId,
            'user_id' => $userId,
            'user_type' => $userType,
            'username' => $username,
            'comment_text' => $validated['comment_text'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment,
                'username' => $username,
                'created_at' => $comment->created_at->format('M d, Y H:i')
            ]);
        }

        // fallback for non-AJAX (optional)
        return redirect()->route('complaints.show', ['complaint' => $complaintId])
                        ->with('success', 'Comment added successfully!');
    }



    // Show form to edit a complaint
    public function edit(Request $request, $id)
    {
        $studentId = $request->query('student_id');
        $complaint = Complaint::where('student_id', $studentId)->findOrFail($id);
        return view('student.complaint_edit', compact('complaint', 'studentId'));
    }

    // Update a complaint
    public function update(Request $request, $id)
    {
        $studentId = $request->input('student_id');

        $complaint = Complaint::where('student_id', $studentId)->findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:50',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('complaint_photos', 'public');
            $complaint->photo = $photoPath;
        }

        $complaint->title = $validated['title'];
        $complaint->description = $validated['description'];
        $complaint->category = $validated['category'];
        $complaint->save();

        return redirect()->route('complaints.dashboard', ['student_id' => $studentId])
            ->with('success', 'Complaint updated successfully!');
    }

    // Delete a complaint
    public function destroy(Request $request, $id)
    {
        $studentId = $request->input('student_id');
        $complaint = Complaint::where('student_id', $studentId)->findOrFail($id);
        $complaint->delete();

        return redirect()->route('complaints.dashboard', ['student_id' => $studentId])
            ->with('success', 'Complaint deleted successfully!');
    }

    // Admin Dashboard - Show all complaints
    public function adminDashboard(Request $request)
    {
        $complaints = Complaint::orderBy('created_at', 'desc')->get();
        
        // Calculate statistics
        $totalComplaints = $complaints->count();
        $pendingComplaints = $complaints->where('status', 'pending')->count();
        $inProgressComplaints = $complaints->where('status', 'in_progress')->count();
        $resolvedComplaints = $complaints->where('status', 'resolved')->count();

        return view('admin.dashboard', compact('complaints', 'totalComplaints', 'pendingComplaints', 'inProgressComplaints', 'resolvedComplaints'));
    }

    // Admin Complaint Detail
    public function adminComplaintDetail($complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        $comments = $complaint->comments()->orderBy('created_at', 'asc')->get();
        
        return view('admin.complaint_detail', compact('complaint', 'comments'));
    }

    // Admin Complaint Assign
    public function adminComplaintAssign($complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        // Mock AI analysis data (in real implementation, this would come from AI service)
        $detectedKeywords = ['facility', 'broken', 'repair'];
        $detectedCategory = 'Facility';
        $confidence = '85%';
        $duplicateComplaints = [];
        $recommendedPriority = 'medium';
        $priorityReason = 'Based on keyword analysis and category matching';
        
        return view('admin.complaint_assign', compact('complaint', 'detectedKeywords', 'detectedCategory', 'confidence', 'duplicateComplaints', 'recommendedPriority', 'priorityReason'));
    }

    // Admin Complaint Assign Post
    public function adminComplaintAssignPost(Request $request, $complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        $validated = $request->validate([
            'department' => 'required|string',
            'assigned_to' => 'required|string',
            'priority' => 'required|string',
            'status' => 'required|string',
            'assignment_notes' => 'nullable|string',
        ]);

        // Update complaint with assignment
        $complaint->update([
            'department' => $validated['department'],
            'assigned_to' => $validated['assigned_to'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'assigned_at' => now(),
        ]);

        // Add assignment comment
        if ($validated['assignment_notes']) {
            Comment::create([
                'comment_id' => Str::uuid()->toString(),
                'complaint_id' => $complaintId,
                'user_id' => null,
                'user_type' => 'admin',
                'username' => 'Admin',
                'comment_text' => 'Assigned to ' . $validated['assigned_to'] . ' in ' . $validated['department'] . '. Notes: ' . $validated['assignment_notes'],
            ]);
        }

        return redirect()->route('admin.complaint.detail', $complaintId)
            ->with('success', 'Complaint assigned successfully!');
    }

    // Admin Complaint Update
    public function adminComplaintUpdate($complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        // Get status history (in real implementation, this would come from a status history table)
        $statusHistory = collect([]);
        
        return view('admin.complaint_update', compact('complaint', 'statusHistory'));
    }

    // Admin Complaint Update Post
    public function adminComplaintUpdatePost(Request $request, $complaintId)
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        $validated = $request->validate([
            'new_status' => 'required|string',
            'actions_taken' => 'nullable|string',
            'resolution' => 'nullable|string',
            'time_spent' => 'nullable|numeric',
            'cost_incurred' => 'nullable|numeric',
            'follow_up_required' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'internal_notes' => 'nullable|string',
        ]);

        // Update complaint status
        $updateData = [
            'status' => $validated['new_status'],
        ];

        if ($validated['new_status'] === 'resolved' || $validated['new_status'] === 'closed') {
            $updateData['resolved_at'] = now();
            $updateData['resolution'] = $validated['resolution'];
        }

        $complaint->update($updateData);

        // Add status update comment
        $commentText = 'Status updated to: ' . ucfirst($validated['new_status']);
        if ($validated['actions_taken']) {
            $commentText .= '. Actions taken: ' . $validated['actions_taken'];
        }
        if ($validated['resolution']) {
            $commentText .= '. Resolution: ' . $validated['resolution'];
        }

        Comment::create([
            'comment_id' => Str::uuid()->toString(),
            'complaint_id' => $complaintId,
            'user_id' => null,
            'user_type' => 'admin',
            'username' => 'Admin',
            'comment_text' => $commentText,
        ]);

        return redirect()->route('admin.complaint.detail', $complaintId)
            ->with('success', 'Complaint status updated successfully!');
    }

    // Admin Add Comment
    public function adminAddComment(Request $request, $complaintId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        // Get admin user information
        $user = Auth::user();
        $username = 'Admin';
        $userId = null;

        if ($user && $user->isAdmin()) {
            $userId = $user->id;
            $username = $user->admin ? $user->admin->name : $user->email;
        }

        Comment::create([
            'comment_id' => Str::uuid()->toString(),
            'complaint_id' => $complaintId,
            'user_id' => $userId,
            'user_type' => 'admin',
            'username' => $username,
            'comment_text' => $validated['content'],
        ]);

        return redirect()->route('admin.complaint.detail', $complaintId)
            ->with('success', 'Comment added successfully!');
    }

    // Student Community Dashboard
    public function studentCommunity(Request $request)
    {
        // Get public complaints with community features
        $query = Complaint::where('is_public', true)
            ->leftJoin('students', 'complaints.student_id', '=', 'students.student_id')
            ->select('complaints.*', 'students.name as student_name')
            ->with(['comments'])
            ->withCount('comments');

        // Add upvote status for authenticated user
        if (Auth::check()) {
            $query->addSelect(['userHasUpvoted' => function($q) {
                $q->selectRaw('COUNT(*) > 0')
                  ->from('complaint_upvotes')
                  ->whereColumn('complaint_upvotes.complaint_id', 'complaints.complaint_id')
                  ->where('complaint_upvotes.student_id', Auth::user()->student_id ?? '');
            }]);
        }

        $complaints = $query->orderBy('complaints.created_at', 'desc')->paginate(12);

        // Calculate statistics
        $totalComplaints = Complaint::where('is_public', true)->count();
        $resolvedComplaints = Complaint::where('is_public', true)->where('status', 'resolved')->count();
        $activeComplaints = $totalComplaints - $resolvedComplaints;

        return view('student.community', compact('complaints', 'totalComplaints', 'resolvedComplaints', 'activeComplaints'));
    }

    // Toggle upvote for a complaint
    public function toggleUpvote(Request $request, $complaintId)
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $studentId = Auth::user()->student_id;
        $complaint = Complaint::findOrFail($complaintId);

        // Check if user already upvoted
        $existingUpvote = \DB::table('complaint_upvotes')
            ->where('complaint_id', $complaintId)
            ->where('student_id', $studentId)
            ->first();

        if ($existingUpvote) {
            // Remove upvote
            \DB::table('complaint_upvotes')
                ->where('complaint_id', $complaintId)
                ->where('student_id', $studentId)
                ->delete();
            
            $complaint->decrement('upvotes');
        } else {
            // Add upvote
            \DB::table('complaint_upvotes')->insert([
                'complaint_id' => $complaintId,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $complaint->increment('upvotes');
        }

        return response()->json([
            'success' => true,
            'upvotes' => $complaint->fresh()->upvotes,
            'hasUpvoted' => !$existingUpvote
        ]);
    }
}
