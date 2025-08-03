<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    protected $primaryKey = 'comment_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        // 'comment_id',
        'complaint_id',
        'user_id',
        'user_type',
        'username',
        'comment_text',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class, 'complaint_id', 'complaint_id');
    }
}
