<?php

namespace App\Services;

use App\Contracts\ComplaintServiceInterface;
use App\Models\Complaint;
use App\Models\Student;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ComplaintService implements ComplaintServiceInterface
{
    public function createComplaint(array $data): Complaint
    {
        DB::beginTransaction();
        
        try {
            // Handle file upload if present
            $photoPath = null;
            if (isset($data['photo']) && $data['photo']) {
                $photoPath = $this->handlePhotoUpload($data['photo']);
            }

            // Create complaint
            $complaint = Complaint::create([
                'student_id' => $data['student_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'priority' => $data['priority'],
                'photo' => $photoPath,
                'status' => 'pending',
            ]);

            // Log complaint creation
            $this->logComplaintActivity($complaint, 'created');

            // Send notifications (if needed)
            $this->notifyComplaintCreated($complaint);

            DB::commit();
            
            return $complaint;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Complaint creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getStudentComplaints(string $studentId, array $filters = []): LengthAwarePaginator
    {
        $query = Complaint::where('student_id', $studentId)
            ->orderBy('created_at', 'desc');

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        return $query->paginate(10);
    }

    public function getPublicComplaints(array $filters = []): LengthAwarePaginator
    {
        $query = Complaint::leftJoin('students', 'complaints.student_id', '=', 'students.student_id')
            ->select(
                'complaints.*', 
                'students.name as student_name'
            )
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
        } else {
            $query->addSelect([DB::raw('0 as userHasUpvoted')]);
        }

        // Apply filters
        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('complaints.created_at', 'desc')->paginate(12);
    }

    public function updateComplaintStatus(string $complaintId, string $status, string $resolution = null): Complaint
    {
        DB::beginTransaction();
        
        try {
            $complaint = Complaint::findOrFail($complaintId);
            
            $oldStatus = $complaint->status;
            $complaint->status = $status;
            
            if ($resolution) {
                $complaint->resolution = $resolution;
            }
            
            if ($status === 'resolved') {
                $complaint->resolved_at = now();
            }
            
            $complaint->save();

            // Log status change
            $this->logComplaintActivity($complaint, 'status_changed', [
                'old_status' => $oldStatus,
                'new_status' => $status
            ]);

            // Send notifications
            $this->notifyStatusChanged($complaint, $oldStatus, $status);

            DB::commit();
            
            return $complaint;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Status update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function toggleUpvote(string $complaintId, string $studentId): array
    {
        $complaint = Complaint::findOrFail($complaintId);
        
        $existingUpvote = DB::table('complaint_upvotes')
            ->where('complaint_id', $complaintId)
            ->where('student_id', $studentId)
            ->first();

        if ($existingUpvote) {
            // Remove upvote
            DB::table('complaint_upvotes')
                ->where('complaint_id', $complaintId)
                ->where('student_id', $studentId)
                ->delete();
            
            $complaint->decrement('upvotes');
            $hasUpvoted = false;
        } else {
            // Add upvote
            DB::table('complaint_upvotes')->insert([
                'complaint_id' => $complaintId,
                'student_id' => $studentId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $complaint->increment('upvotes');
            $hasUpvoted = true;
        }

        return [
            'success' => true,
            'upvotes' => $complaint->fresh()->upvotes,
            'hasUpvoted' => $hasUpvoted
        ];
    }

    public function getComplaintStatistics(string $studentId = null): array
    {
        $query = Complaint::query();
        
        if ($studentId) {
            $query->where('student_id', $studentId);
        }
        // No need to filter by is_public since all complaints are now public

        $total = $query->count();
        $resolved = $query->where('status', 'resolved')->count();
        $active = $total - $resolved;
        $successRate = $total > 0 ? round(($resolved / $total) * 100) : 0;

        return [
            'total' => $total,
            'resolved' => $resolved,
            'active' => $active,
            'success_rate' => $successRate
        ];
    }

    public function validateComplaintData(array $data): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,critical',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        return validator($data, $rules)->validate();
    }

    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function handlePhotoUpload($photo): string
    {
        return $photo->store('complaint_photos', 'public');
    }

    private function logComplaintActivity(Complaint $complaint, string $action, array $metadata = []): void
    {
        Log::info("Complaint {$action}", [
            'complaint_id' => $complaint->complaint_id,
            'student_id' => $complaint->student_id,
            'action' => $action,
            'metadata' => $metadata
        ]);
    }

    private function notifyComplaintCreated(Complaint $complaint): void
    {
        Log::info("Notification: New complaint created", [
            'complaint_id' => $complaint->complaint_id,
            'title' => $complaint->title
        ]);
    }

    private function notifyStatusChanged(Complaint $complaint, string $oldStatus, string $newStatus): void
    {
        Log::info("Notification: Complaint status changed", [
            'complaint_id' => $complaint->complaint_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);
    }

    public function getComplaintById(string $complaintId): Complaint
    {
        return Complaint::findOrFail($complaintId);
    }

    public function deleteComplaint(string $complaintId): bool
    {
        DB::beginTransaction();
        
        try {
            $complaint = Complaint::findOrFail($complaintId);
            
            // Delete associated photo if exists
            if ($complaint->photo) {
                Storage::disk('public')->delete($complaint->photo);
            }
            
            // Log deletion
            $this->logComplaintActivity($complaint, 'deleted');
            
            // Delete the complaint
            $deleted = $complaint->delete();
            
            DB::commit();
            
            return $deleted;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Complaint deletion failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function addComment(string $complaintId, array $commentData): mixed
    {
        DB::beginTransaction();
        
        try {
            $complaint = Complaint::findOrFail($complaintId);
            
            $comment = Comment::create([
                'complaint_id' => $complaintId,
                'student_id' => $commentData['student_id'],
                'comment' => $commentData['comment'],
            ]);
            
            // Log comment addition
            $this->logComplaintActivity($complaint, 'comment_added', [
                'comment_id' => $comment->id
            ]);
            
            DB::commit();
            
            return $comment;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Comment creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getComplaintsByCategory(string $category, array $filters = []): LengthAwarePaginator
    {
        $query = Complaint::where('category', $category);
        
        // Apply additional filters
        $query = $this->applyFilters($query, $filters);
        
        return $query->orderBy('created_at', 'desc')->paginate(12);
    }

    public function getComplaintsByPriority(string $priority, array $filters = []): LengthAwarePaginator
    {
        $query = Complaint::where('priority', $priority);
        
        // Apply additional filters
        $query = $this->applyFilters($query, $filters);
        
        return $query->orderBy('created_at', 'desc')->paginate(12);
    }
}
