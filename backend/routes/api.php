<?php

use App\Http\Controllers\Api\V1\ReviewController;

Route::prefix('v1')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('/reviews', [ReviewController::class, 'index'])
        ->middleware('throttle:60,1');

    Route::get('/reviews/{review}', [ReviewController::class, 'replies'])
        ->middleware('throttle:60,1');
});
