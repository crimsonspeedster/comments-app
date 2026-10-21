<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\ReviewCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexReviewRequest;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\ReviewAttachmentService;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Throwable;
use JsonException;

class ReviewController extends Controller
{
    private const int CACHE_TTL = 300;

    private const array INDEX_CACHE_TAGS = [
        'reviews',
        'reviews:index',
    ];

    /**
     * @throws JsonException
     */
    public function index(IndexReviewRequest $request)
    {
        $data = $request->validated();

        $perPage = min((int) ($data['per_page'] ?? 25), 25);
        $sortBy = $data['sort_by'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        $page = (int) $request->input('page', 1);

        $cacheKey = 'reviews:index:' . md5(json_encode([
            'per_page' => $perPage,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
            'page' => $page,
        ], JSON_THROW_ON_ERROR));

        $reviews = Cache::tags(self::INDEX_CACHE_TAGS)
            ->remember(
                $cacheKey,
                self::CACHE_TTL,
                fn () => Review::query()
                    ->whereNull('parent_id')
                    ->with('attachments')
                    ->withCount('replies')
                    ->orderBy($sortBy, $sortDirection)
                    ->paginate(
                        $perPage,
                        ['*'],
                        'page',
                        $page,
                    ),
            );

        return ReviewResource::collection($reviews);
    }

    /**
     * @throws JsonException
     */
    public function replies(
        Review $review,
        IndexReviewRequest $request,
    ) {
        $data = $request->validated();

        $perPage = min((int) ($data['per_page'] ?? 25), 25);
        $page = (int) $request->input('page', 1);

        $cacheKey = 'reviews:replies:' . md5(json_encode([
            'review_id' => $review->id,
            'per_page' => $perPage,
            'page' => $page,
        ], JSON_THROW_ON_ERROR));

        $replies = Cache::tags([
            'reviews',
            "reviews:replies:{$review->id}",
        ])->remember(
            $cacheKey,
            self::CACHE_TTL,
            fn () => $review->replies()
                ->with('attachments')
                ->withCount('replies')
                ->latest()
                ->paginate(
                    $perPage,
                    ['*'],
                    'page',
                    $page,
                ),
        );

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

            event(new ReviewCreated($review));
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
