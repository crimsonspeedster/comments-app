<?php

namespace App\OpenApi\ResponseItems;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ReviewItem',
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            example: 1
        ),
        new OA\Property(
            property: 'parent_id',
            type: 'integer',
            example: null,
            nullable: true
        ),
        new OA\Property(
            property: 'user_name',
            type: 'string',
            example: 'John Doe'
        ),
        new OA\Property(
            property: 'email',
            type: 'string',
            format: 'email',
            example: 'john@example.com'
        ),
        new OA\Property(
            property: 'url',
            type: 'string',
            example: 'https://example.com',
            nullable: true
        ),
        new OA\Property(
            property: 'text',
            type: 'string',
            example: 'This is a review.'
        ),
        new OA\Property(
            property: 'created_at',
            type: 'string',
            format: 'date-time',
            example: '2026-10-20T12:30:00.000000Z'
        ),
        new OA\Property(
            property: 'has_replies',
            type: 'boolean',
            example: true
        ),
        new OA\Property(
            property: 'attachments',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/ReviewAttachmentItem'
            )
        ),
    ],
    type: 'object'
)]
class ReviewItem {}
