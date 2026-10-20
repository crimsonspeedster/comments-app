<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'parent_id',
        'user_name',
        'email',
        'url',
        'text',
    ];

    public function attachments(): HasMany
    {
        return $this->hasMany(
            ReviewAttachment::class,
        );
    }

    public function replies (): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id',
        );
    }

    public function parentReview (): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id',
        );
    }
}
