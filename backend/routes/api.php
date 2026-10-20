<?php

use App\Http\Controllers\Api\V1\ReviewController;

Route::prefix('v1')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);

    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/{review}', [ReviewController::class, 'replies']);
});
