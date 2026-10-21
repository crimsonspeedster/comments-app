<?php

namespace App\Listeners;

use App\Events\ReviewCreated;
use App\Jobs\IndexReviewJob;

class IndexReviewListener
{
    public function handle(ReviewCreated $event): void
    {
        IndexReviewJob::dispatch($event->review);
    }
}
