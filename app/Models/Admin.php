<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'department',
        'position',
    ];

    /**
     * Get the user account for this admin.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
