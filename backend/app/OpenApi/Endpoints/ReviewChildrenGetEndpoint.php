<?php

namespace App\OpenApi\Endpoints;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/reviews/{review}',
    description: 'Get paginated replies for a specific review, including their attachments and reply counts.',
    summary: 'Get review replies',
    security: [],
    tags: ['reviews'],
    parameters: [
        new OA\Parameter(
            name: 'review',
            description: 'ID of the parent review',
            in: 'path',
            required: true,
            schema: new OA\Schema(
                type: 'integer',
                minimum: 1
            ),
            example: 1
        ),
        new OA\Parameter(
            name: 'page',
            description: 'Pagination page number',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'integer',
                default: 1,
                minimum: 1
            ),
            example: 1
        ),
        new OA\Parameter(
            name: 'per_page',
            description: 'Number of replies per page (max 25)',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'integer',
                default: 25,
                maximum: 25,
                minimum: 1
            ),
            example: 25
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Replies retrieved successfully',
            content: new OA\JsonContent(
                ref: '#/components/schemas/ReviewPaginationResponse'
            )
        ),
        new OA\Response(
            response: 404,
            description: 'Review not found'
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error'
        ),
    ]
)]
class ReviewChildrenGetEndpoint {}
