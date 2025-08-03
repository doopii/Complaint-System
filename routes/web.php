<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('home');
})->name('home');

// Protected complaint routes - require authentication
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ComplaintController::class, 'dashboard'])->name('complaints.dashboard');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints/{complaint}/comments', [ComplaintController::class, 'addComment'])->name('complaints.addComment');
    Route::resource('complaints', ComplaintController::class);
});

// Backward compatibility routes (no auth required for old URLs)
Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index.old');

// Admin routes - protected by admin middleware
Route::prefix('admin')->middleware('admin')->group(function () {
    Route::get('/dashboard', [ComplaintController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/complaints/{complaint}/detail', [ComplaintController::class, 'adminComplaintDetail'])->name('admin.complaint.detail');
    Route::get('/complaints/{complaint}/assign', [ComplaintController::class, 'adminComplaintAssign'])->name('admin.complaint.assign');
    Route::post('/complaints/{complaint}/assign', [ComplaintController::class, 'adminComplaintAssignPost'])->name('admin.complaint.assign.post');
    Route::get('/complaints/{complaint}/update', [ComplaintController::class, 'adminComplaintUpdate'])->name('admin.complaint.update');
    Route::post('/complaints/{complaint}/update', [ComplaintController::class, 'adminComplaintUpdatePost'])->name('admin.complaint.update.post');
    Route::post('/complaints/{complaint}/comments', [ComplaintController::class, 'adminAddComment'])->name('admin.complaint.addComment');
});

// Student Community Routes
Route::middleware('auth')->group(function () {
    Route::get('/student/community', [ComplaintController::class, 'studentCommunity'])->name('student.community');
    Route::post('/complaints/{complaint}/upvote', [ComplaintController::class, 'toggleUpvote'])->name('complaints.upvote');
});
