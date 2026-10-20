<?php

namespace App\Models;

use App\Enums\AttachmentStatus;
use Illuminate\Database\Eloquent\Model;

class ReviewAttachment extends Model
{
    protected $fillable = [
        'status',
        'review_id',
        'original_path',
        'processed_path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'status' => AttachmentStatus::class,
    ];
}
