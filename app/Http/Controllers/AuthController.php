<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                return redirect()->intended(route('complaints.dashboard', ['student_id' => $user->student_id]));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Show registration form
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Handle registration
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'student_id' => 'required_if:account_type,student|nullable|string|max:20|unique:students,student_id',
            'account_type' => 'required|in:student,admin',
            'password' => 'required|string|min:8|confirmed',
            // Additional fields for student
            'course' => 'required_if:account_type,student|nullable|string|max:255',
            'year_level' => 'required_if:account_type,student|nullable|integer|min:1|max:6',
            // Additional fields for admin
            'department' => 'required_if:account_type,admin|nullable|string|max:255',
            'position' => 'required_if:account_type,admin|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        
        try {
            // Create user account first
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => $request->account_type,
            ]);

            if ($request->account_type === 'student') {
                // Create student profile
                Student::create([
                    'student_id' => $request->student_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'course' => $request->course,
                    'year_level' => $request->year_level,
                    'department' => $request->department,
                ]);
            } else {
                // Create admin profile
                Admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'department' => $request->department,
                    'position' => $request->position,
                ]);
            }

            DB::commit();
            Auth::login($user);

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('complaints.dashboard', ['student_id' => $user->student_id]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
