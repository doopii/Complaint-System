<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $table = 'complaints';

    protected $primaryKey = 'complaint_id';

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'category',
        'photo',
        'status',
        'priority',
        'department',
        'assigned_to',
        'assigned_at',
        'resolved_at',
        'resolution',
        'image_path'
    ];

    // Add this relationship method here:
    public function comments()
    {
        return $this->hasMany(Comment::class, 'complaint_id', 'complaint_id');
    }
}

