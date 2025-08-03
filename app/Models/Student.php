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
}
