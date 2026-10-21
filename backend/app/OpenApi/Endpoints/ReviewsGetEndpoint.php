<?php

namespace App\OpenApi\Endpoints;

use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/api/v1/reviews',
    description: 'Get root reviews with attachments and pagination.',
    summary: 'Get reviews',
    security: [],
    tags: ['reviews'],
    parameters: [
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
            description: 'Number of reviews per page (max 25)',
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
        new OA\Parameter(
            name: 'sort_by',
            description: 'Field used to sort reviews',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'string',
                default: 'created_at',
                enum: [
                    'user_name',
                    'email',
                    'created_at',
                ]
            ),
            example: 'created_at'
        ),
        new OA\Parameter(
            name: 'sort_direction',
            description: 'Sorting direction',
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'string',
                default: 'desc',
                enum: [
                    'asc',
                    'desc',
                ]
            ),
            example: 'desc'
        ),
    ],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Reviews retrieved successfully',
            content: new OA\JsonContent(
                ref: '#/components/schemas/ReviewPaginationResponse'
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error'
        ),
    ]
)]
class ReviewsGetEndpoint {}
