<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();
        
        if (!$student) {
            return redirect()->route('home')->with('error', 'Student profile not found.');
        }
        
        return view('student.profile', compact('student'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();
        
        if (!$student) {
            return redirect()->route('home')->with('error', 'Student profile not found.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'year_level' => 'nullable|integer|min:1|max:6',
            'department' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ], [
            'profile_picture.max' => 'Profile picture must be less than 2MB.',
            'profile_picture.mimes' => 'Profile picture must be a valid image file (JPEG, PNG, JPG, GIF, WEBP).',
            'bio.max' => 'Bio cannot exceed 1000 characters.',
            'year_level.min' => 'Year level must be at least 1.',
            'year_level.max' => 'Year level cannot exceed 6.',
        ]);
        
        $data = $request->only(['name', 'course', 'year_level', 'department', 'bio']);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture)) {
                Storage::disk('public')->delete($student->profile_picture);
            }
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists('profile_pictures')) {
                Storage::disk('public')->makeDirectory('profile_pictures');
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }
        
        $student->update($data);
        
        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}
