<?php

namespace App\Listeners;


use App\Events\AttachmentStatusChanged;
use App\Events\AttachmentStatusChangedBroadcast;
use App\Events\ReviewCreated;
use App\Events\ReviewCreatedBroadcast;

class BroadcastAttachmentsStatusChanged
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
    public function handle(AttachmentStatusChanged $event): void
    {
        event(new AttachmentStatusChangedBroadcast($event->attachment));
    }
}
