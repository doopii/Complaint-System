<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class HigherManagementController extends Controller
{
    // Show the main Higher Management dashboard
    public function index()
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
        }

        return view('admin.higher_management.index');
    }

    //Show the Analytics Dashboard
    public function analyticsDashboard()
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
        }

        // Get basic statistics for now (can be enhanced later)
        $totalComplaints = Complaint::count();
        $pendingComplaints = Complaint::where('status', 'pending')->count();
        $inProgressComplaints = Complaint::where('status', 'in_progress')->count();
        $resolvedComplaints = Complaint::where('status', 'resolved')->count();
        $closedComplaints = Complaint::where('status', 'closed')->count();

        // Category breakdown
        $categoryStats = Complaint::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // Priority breakdown
        $priorityStats = Complaint::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->get();

        // Monthly trend (last 6 months)
        $monthlyTrend = Complaint::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.higher_management.analytics_dashboard', compact(
            'totalComplaints',
            'pendingComplaints',
            'inProgressComplaints',
            'resolvedComplaints',
            'closedComplaints',
            'categoryStats',
            'priorityStats',
            'monthlyTrend'
        ));
    }

    //Show the Notification Settings page
    public function notificationSettings()
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
        }

        return view('admin.higher_management.notification_settings');
    }

    //Show the Unresolved Issues page
    public function unresolvedIssues()
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
        }

        // Get unresolved complaints (pending and in_progress)
        $unresolvedComplaints = Complaint::whereIn('status', ['pending', 'in_progress'])
            ->with(['comments'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by priority for better organization
        $urgentIssues = $unresolvedComplaints->where('priority', 'urgent');
        $highPriorityIssues = $unresolvedComplaints->where('priority', 'high');
        $mediumPriorityIssues = $unresolvedComplaints->where('priority', 'medium');
        $lowPriorityIssues = $unresolvedComplaints->where('priority', 'low');

        return view('admin.higher_management.unresolved_issues', compact(
            'unresolvedComplaints',
            'urgentIssues',
            'highPriorityIssues',
            'mediumPriorityIssues',
            'lowPriorityIssues'
        ));
    }
}
