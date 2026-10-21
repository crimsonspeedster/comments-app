<?php

namespace App\Services;

use App\Enums\AttachmentStatus;
use App\Events\AttachmentStatusChanged;
use App\Events\AttachmentStatusChangedBroadcast;
use App\Jobs\ProcessReviewAttachment;
use App\Models\Review;
use App\Models\ReviewAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\DriverException;
use Intervention\Image\Exceptions\ImageDecoderException;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Intervention\Image\Exceptions\MissingDependencyException;
use Intervention\Image\ImageManager;
use Throwable;

class ReviewAttachmentService
{
    /**
     * @throws Throwable
     */
    public function store(Review $review, UploadedFile $file): ReviewAttachment
    {
        $path = null;

        try {
            $mimeType = $file->getMimeType();

            $path = $file->store(
                'reviews/' . $review->id . '/originals',
                'public'
            );

            $attachment = $review->attachments()->create([
                'original_path' => $path,
                'processed_path' => null,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
                'size' => Storage::disk('public')->size($path),
                'status' => AttachmentStatus::Processing,
            ]);

            if ($mimeType === 'text/plain') {
                $attachment->update([
                    'processed_path' => $path,
                ]);

                $this->updateStatus(
                    $attachment,
                    AttachmentStatus::Completed,
                );

                return $attachment;
            }

            ProcessReviewAttachment::dispatch($attachment->id)
                ->afterCommit();

            return $attachment;
        } catch (Throwable $exception) {
            if ($path !== null) {
                Storage::disk('public')->delete($path);
            }

            Log::error('Review attachment upload failed.', [
                'review_id' => $review->id,
                'original_name' => $file->getClientOriginalName(),
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    /**
     * @throws Throwable
     * @throws ImageDecoderException
     * @throws MissingDependencyException
     * @throws DriverException
     * @throws InvalidArgumentException
     */
    public function process(ReviewAttachment $attachment): void
    {
        $disk = Storage::disk('public');
        $processedPath = null;

        try {
            $manager = new ImageManager(new Driver());

            $image = $manager->decodePath(
                $disk->path($attachment->original_path)
            );

            $image->scaleDown(320, 240);

            $encoded = $image->encodeUsingMediaType(
                $attachment->mime_type
            );

            $processedPath = 'reviews/' . $attachment->review_id
                . '/processed/' . uniqid() . '.'
                . pathinfo($attachment->original_name, PATHINFO_EXTENSION);

            $disk->put($processedPath, (string) $encoded);

            $attachment->update([
                'processed_path' => $processedPath,
                'size' => Storage::disk('public')->size($processedPath),
            ]);

            $this->updateStatus(
                $attachment,
                AttachmentStatus::Completed,
            );

            $disk->delete($attachment->original_path);
        } catch (Throwable $exception) {
            if ($processedPath !== null) {
                $disk->delete($processedPath);
            }

            Log::error('Review attachment processing attempt failed.', [
                'attachment_id' => $attachment->id,
                'exception' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function deleteFiles(array $paths): void
    {
        $disk = Storage::disk('public');

        foreach ($paths as $path) {
            try {
                $disk->delete($path);
            } catch (Throwable $exception) {
                Log::error('Failed to delete attachment file after transaction rollback.', [
                    'path' => $path,
                    'exception' => $exception->getMessage(),
                ]);
            }
        }
    }

    public function updateStatus(
        ReviewAttachment $attachment,
        AttachmentStatus $status,
    ): void {
        $attachment->update([
            'status' => $status,
        ]);

        event(new AttachmentStatusChanged($attachment->fresh()));
    }
}
