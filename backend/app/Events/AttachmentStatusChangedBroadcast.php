<?php

namespace App\Events;

use App\Http\Resources\ReviewAttachmentResource;
use App\Models\ReviewAttachment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachmentStatusChangedBroadcast implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ReviewAttachment $attachment,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('reviews'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'attachment.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'data' => new ReviewAttachmentResource($this->attachment)->resolve(),
            'review' => [
                'id' => $this->attachment->review_id,
                'parent_id' => $this->attachment->review?->parent_id,
            ],
        ];
    }
}
