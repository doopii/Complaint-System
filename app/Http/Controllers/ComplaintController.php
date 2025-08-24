<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Comment;
use App\Contracts\ComplaintServiceInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class ComplaintController extends Controller
{
    protected ComplaintServiceInterface $complaintService;

    public function __construct(ComplaintServiceInterface $complaintService)
    {
        $this->complaintService = $complaintService;
    }

    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->isStudent()) {
            return redirect()->route('complaints.dashboard');
        }
        
        $studentId = $request->query('student_id');
        if ($studentId) {
            return redirect()->route('complaints.dashboard', ['student_id' => $studentId]);
        }
        
        return redirect()->route('login');
    }

    public function dashboard(Request $request)
    {
        if (Auth::check() && Auth::user()->isStudent()) {
            $user = Auth::user();
            $studentId = $user->student_id;
            
            // Get filters from request
            $filters = $request->only(['status', 'category', 'priority', 'date_from', 'date_to']);
            
            // Use facade to get complaints
            $complaints = $this->complaintService->getStudentComplaints($studentId, $filters);
                
            return view('student.complaint_dashboard', compact('complaints', 'studentId'));
        }
        
        // For backward compatibility with old URLs
        $studentId = $request->query('student_id'); 
        $complaints = collect(); // Empty collection for non-authenticated users
        
        if ($studentId) {
            // Get filters from request
            $filters = $request->only(['status', 'category', 'priority', 'date_from', 'date_to']);
            
            // Use facade to get complaints
            $complaints = $this->complaintService->getStudentComplaints($studentId, $filters);
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
        try {
            $studentId = null;
            if (Auth::check() && Auth::user()->isStudent()) {
                $studentId = Auth::user()->student_id;
            } else {
                $validated = $request->validate([
                    'student_id' => 'required|string|max:20',
                ]);
                $studentId = $validated['student_id'];
            }

            // Prepare data for the facade
            $data = $request->all();
            $data['student_id'] = $studentId;
            
            // Validate data using the facade
            $validatedData = $this->complaintService->validateComplaintData($data);
            $validatedData['student_id'] = $studentId;

            // Create complaint using the facade
            $complaint = $this->complaintService->createComplaint($validatedData);

            // Redirect based on authentication status
            if (Auth::check()) {
                return redirect()->route('complaints.dashboard')
                    ->with('success', 'Complaint submitted successfully!');
            } else {
                return redirect()->route('complaints.dashboard', ['student_id' => $studentId])
                    ->with('success', 'Complaint submitted successfully!');
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to submit complaint. Please try again.'])
                        ->withInput();
        }
    }

    // Show a single complaint by ID 
    public function show(Request $request, $complaint)
    {
        // Get student_id from authenticated user or query param
        $studentId = null;
        if (Auth::check() && Auth::user()->isStudent()) {
            $studentId = Auth::user()->student_id;
        } else {
            $studentId = $request->query('student_id');
        }

        // Find complaint - allow viewing any complaint in community
        $complaint = Complaint::findOrFail($complaint);

        // Get comments for this complaint (latest first)
        $comments = $complaint->comments()->orderBy('created_at', 'desc')->get();

        // Calculate progress width based on status
        $progressWidth = 33; 
        switch($complaint->status) {
            case 'pending':
                $progressWidth = 33;
                break;
            case 'in_progress':
                $progressWidth = 66;
                break;
            case 'resolved':
                $progressWidth = 100;
                break;
            default:
                $progressWidth = 33;
        }

        return view('student.complaint_detail', compact('complaint', 'studentId', 'comments', 'progressWidth'));
    }

    public function addComment(Request $request, $complaintId)
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to add comments.');
        }

        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        // Get authenticated user information
        $user = Auth::user();
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

        return redirect()->back()
            ->with('success', 'Comment added successfully!');
    }

    // Student Community Dashboard
    public function studentCommunity(Request $request)
    {
        // Get filters from request 
        $filters = $request->only(['status', 'category', 'priority']);
        
        // Use facade to get public complaints
        $complaints = $this->complaintService->getPublicComplaints($filters);

        // Use facade to get statistics
        $stats = $this->complaintService->getComplaintStatistics();
        
        return view('student.community', [
            'complaints' => $complaints,
            'totalComplaints' => $stats['total'],
            'resolvedComplaints' => $stats['resolved'],
            'activeComplaints' => $stats['active']
        ]);
    }

    // Toggle upvote for a complaint
    public function toggleUpvote(Request $request, $complaintId)
    {
        if (!Auth::check() || !Auth::user()->isStudent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $studentId = Auth::user()->student_id;
            
            // Use facade to handle upvote toggle
            $result = $this->complaintService->toggleUpvote($complaintId, $studentId);
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to toggle upvote'
            ], 500);
        }
    }
}
