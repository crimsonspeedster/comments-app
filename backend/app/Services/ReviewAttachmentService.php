<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Intervention\Image\Exceptions\MissingDependencyException;
use Intervention\Image\ImageManager;
use Throwable;

class ReviewAttachmentService
{
    /**
     * @throws Throwable
     * @throws InvalidArgumentException
     * @throws MissingDependencyException
     */
    public function store(Review $review, UploadedFile $file): void
    {
        $path = null;

        try {
            $mimeType = $file->getMimeType();

            if ($mimeType === 'text/plain') {
                $path = $file->store('reviews/' . $review->id, 'public');
            } else {
                $manager = new ImageManager(new Driver());

                $image = $manager->decodePath($file->getPathname());

                $image->scaleDown(320, 240);

                $encoded = $image->encodeUsingMediaType($mimeType);

                $path = 'reviews/' . $review->id . '/' . uniqid() . '.' .
                    $file->extension();

                Storage::disk('public')->put($path, (string) $encoded);
            }

            $review->attachments()->create([
                'file_path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $mimeType,
                'size' => Storage::disk('public')->size($path),
            ]);
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
}
