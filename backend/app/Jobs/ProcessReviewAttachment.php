<?php

namespace App\Jobs;

use App\Enums\AttachmentStatus;
use App\Events\AttachmentStatusChanged;
use App\Events\AttachmentStatusChangedBroadcast;
use App\Models\ReviewAttachment;
use App\Services\ReviewAttachmentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Exceptions\DriverException;
use Intervention\Image\Exceptions\ImageDecoderException;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Intervention\Image\Exceptions\MissingDependencyException;
use Throwable;

class ProcessReviewAttachment implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 30];

    public function __construct(
        public readonly int $attachmentId,
    ) {}

    /**
     * @throws Throwable
     * @throws ImageDecoderException
     * @throws MissingDependencyException
     * @throws DriverException
     * @throws InvalidArgumentException
     */
    public function handle(
        ReviewAttachmentService $service,
    ): void {
        $attachment = ReviewAttachment::findOrFail($this->attachmentId);

        if ($attachment->status === AttachmentStatus::Completed) {
            return;
        }

        $service->updateStatus(
            $attachment,
            AttachmentStatus::Processing,
        );

        $service->process($attachment);
    }

    public function failed(?Throwable $exception): void
    {
        $attachment = ReviewAttachment::find($this->attachmentId);

        if ($attachment === null) {
            return;
        }

        $attachment->update([
            'status' => AttachmentStatus::Failed,
        ]);

        event(new AttachmentStatusChanged($attachment));

        Log::error('Review attachment processing failed permanently.', [
            'attachment_id' => $this->attachmentId,
            'exception' => $exception?->getMessage(),
        ]);
    }
}
