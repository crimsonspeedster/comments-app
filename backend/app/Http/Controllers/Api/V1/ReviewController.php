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
            ->withCount('replies')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        return ReviewResource::collection(
            $reviews->load('attachments')
        );
    }

    public function replies(
        Review $review,
        IndexReviewRequest $request,
    ) {
        $perPage = $request->validated('per_page') ?? 25;

        $replies = $review->replies()
            ->withCount('replies')
            ->latest()
            ->paginate($perPage);

        return ReviewResource::collection(
            $replies->load('attachments')
        );
    }

    public function store(
        StoreReviewRequest $request,
        ReviewAttachmentService $reviewAttachmentService,
    )
    {
        $data = $request->validated();

        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'a[href|title],code,i,strong');
        $config->set('HTML.Doctype', 'XHTML 1.0 Strict');

        $purifier = new HTMLPurifier($config);

        $data['text'] = $purifier->purify($data['text']);

        $review = Review::create([
            'parent_id' => $data['parent_id'] ?? null,
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'url' => $data['url'] ?? null,
            'text' => $data['text'],
        ]);

        foreach ($request->file('attachments', []) as $attachment) {
            try {
                $reviewAttachmentService->store($review, $attachment);
            }
            catch (Throwable $exception) {
                return response($exception->getMessage(), 500);
            }
        }

        return response()->json([
            'data' => new ReviewResource(
                $review->load('attachments'),
            ),
        ], 201);
    }
}
