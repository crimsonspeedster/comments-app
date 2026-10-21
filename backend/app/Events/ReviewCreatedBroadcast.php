<?php

namespace App\Events;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewCreatedBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Review $review,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('reviews'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'review.created';
    }

    public function broadcastWith(): array
    {
        $this->review->loadMissing('attachments')
            ->loadCount('replies');

        return [
            'data' => new ReviewResource($this->review)->resolve(),
        ];
    }
}
