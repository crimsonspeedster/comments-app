<?php

namespace App\Observers;

use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        //
    }

    public function deleting(Review $review): void
    {
        $filePaths = $review->attachments()
            ->pluck('file_path')
            ->all();

        DB::afterCommit(function () use ($filePaths): void {
            foreach ($filePaths as $filePath) {
                Storage::disk('public')->delete($filePath);
            }
        });
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
