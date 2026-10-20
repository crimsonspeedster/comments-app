<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewAttachment extends Model
{
    protected $fillable = [
        'review_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];
}
