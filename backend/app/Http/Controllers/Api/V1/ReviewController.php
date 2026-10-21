<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexReviewRequest;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewAttachmentService;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReviewController extends Controller
{
    public function index(IndexReviewRequest $request)
    {
        $data = $request->validated();

        $perPage = min((int) ($data['per_page'] ?? 25), 25);
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';

        $reviews = Review::query()
            ->whereNull('parent_id')
            ->with(['attachments'])
            ->withCount('replies')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        return ReviewResource::collection($reviews);
    }

    public function replies(
        Review $review,
        IndexReviewRequest $request,
    ) {
        $perPage = $request->validated('per_page') ?? 25;

        $replies = $review->replies()
            ->with(['attachments'])
            ->withCount('replies')
            ->latest()
            ->paginate($perPage);

        return ReviewResource::collection($replies);
    }

    /**
     * @throws Throwable
     */
    public function store(
        StoreReviewRequest $request,
        ReviewAttachmentService $reviewAttachmentService,
    ) {
        $data = $request->validated();

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'a[href|title],code,i,strong');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');

        $purifier = new HTMLPurifier($config);

        $data['text'] = $purifier->purify($data['text']);

        $storedPaths = [];

        try {
            $review = DB::transaction(function () use (
                $data,
                $request,
                $reviewAttachmentService,
                &$storedPaths,
            ) {
                $review = Review::create([
                    'parent_id' => $data['parent_id'] ?? null,
                    'user_name' => $data['user_name'],
                    'email' => $data['email'],
                    'url' => $data['url'] ?? null,
                    'text' => $data['text'],
                ]);

                foreach ($request->file('attachments', []) as $file) {
                    $attachment = $reviewAttachmentService->store(
                        $review,
                        $file,
                    );

                    $storedPaths[] = $attachment->original_path;
                }

                return $review;
            });
        } catch (Throwable $exception) {
            $reviewAttachmentService->deleteFiles($storedPaths);

            throw $exception;
        }

        return response()->json([
            'data' => new ReviewResource(
                $review->load('attachments')->loadCount('replies'),
            ),
        ], 201);
    }
}
