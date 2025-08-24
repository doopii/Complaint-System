<?php

namespace App\Contracts;

use App\Models\Complaint;
use Illuminate\Pagination\LengthAwarePaginator;

interface ComplaintServiceInterface
{
    public function createComplaint(array $data): Complaint;
    public function getStudentComplaints(string $studentId, array $filters = []): LengthAwarePaginator;
    public function getPublicComplaints(array $filters = []): LengthAwarePaginator;
    public function updateComplaintStatus(string $complaintId, string $status, string $resolution = null): Complaint;
    public function toggleUpvote(string $complaintId, string $studentId): array;
    public function getComplaintStatistics(string $studentId = null): array;
    public function validateComplaintData(array $data): array;
    public function getComplaintById(string $complaintId): Complaint;
    public function deleteComplaint(string $complaintId): bool;
    public function addComment(string $complaintId, array $commentData): mixed;
    public function getComplaintsByCategory(string $category, array $filters = []): LengthAwarePaginator;
    public function getComplaintsByPriority(string $priority, array $filters = []): LengthAwarePaginator;
}
