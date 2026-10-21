<?php

namespace App\Listeners;

use App\Events\AttachmentStatusChanged;
use App\Events\ReviewCreated;
use Illuminate\Support\Facades\Cache;

class InvalidateAttachmentCache
{
    public function handle(AttachmentStatusChanged $event): void
    {
        Cache::tags([
            'reviews',
            'reviews:index',
        ])->flush();

        $review = $event->attachment->review;

        if ($review?->parent_id !== null) {
            Cache::tags([
                'reviews',
                "reviews:replies:{$review->parent_id}",
            ])->flush();
        }
    }
}
