<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if user is a student
     */
    public function isStudent()
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    /**
     * Get the student profile for this user
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'email', 'email');
    }

    /**
     * Get the admin profile for this user
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'email', 'email');
    }

    /**
     * Get the profile based on user type
     */
    public function profile()
    {
        if ($this->isStudent()) {
            return $this->student;
        }
        
        if ($this->isAdmin()) {
            return $this->admin;
        }
        
        return null;
    }

    /**
     * Get the user's name from their profile
     */
    public function getNameAttribute()
    {
        if ($this->isStudent() && $this->student) {
            return $this->student->name;
        }
        
        if ($this->isAdmin() && $this->admin) {
            return $this->admin->name;
        }
        
        return null;
    }

    /**
     * Get the student ID if user is a student
     */
    public function getStudentIdAttribute()
    {
        if ($this->isStudent() && $this->student) {
            return $this->student->student_id;
        }
        
        return null;
    }
}
