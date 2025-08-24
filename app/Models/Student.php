<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'course',
        'year_level',
        'department',
        'profile_picture',
        'bio',
    ];

    /**
     * Get the user account for this student.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get complaints submitted by this student.
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'student_id', 'student_id');
    }

    /**
     * Get the profile picture URL or default placeholder.
     */
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return null;
    }

    /**
     * Get the student's initials for placeholder.
     */
    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->name, 0, 2));
    }
}
