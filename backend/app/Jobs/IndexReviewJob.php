<?php

namespace App\Jobs;

use App\Models\Review;
use App\Services\CommentSearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IndexReviewJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(
        public Review $review
    ) {}

    public function handle(CommentSearchService $service): void
    {
        $service->indexComment([
            'id' => $this->review->id,
            'parent_id' => $this->review->parent_id,
            'user_name' => $this->review->user_name,
            'email' => $this->review->email,
            'url' => $this->review->url,
            'text' => $this->review->text,
            'created_at' => $this->review->created_at,
            'updated_at' => $this->review->updated_at,
        ]);
    }
}
