<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use Illuminate\Support\Facades\Cache;

class InvalidateReviewCache
{
    public function handle(ReviewCreated $event): void
    {
        Cache::tags([
            'reviews',
            'reviews:index',
        ])->flush();

        $review = $event->review;

        if ($review->parent_id !== null) {
            Cache::tags([
                'reviews',
                "reviews:replies:{$review->parent_id}",
            ])->flush();
        }
    }
}
