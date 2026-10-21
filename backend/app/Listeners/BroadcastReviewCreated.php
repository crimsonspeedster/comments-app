<?php

namespace App\Listeners;


use App\Events\ReviewCreated;
use App\Events\ReviewCreatedBroadcast;

class BroadcastReviewCreated
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReviewCreated $event): void
    {
        event(new ReviewCreatedBroadcast($event->review));
    }
}
