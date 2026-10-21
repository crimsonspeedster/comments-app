<?php

namespace App\OpenApi\ResponseItems;

use App\Enums\AttachmentStatus;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ReviewAttachmentItem',
    properties: [
        new OA\Property(
            property: 'id',
            type: 'integer',
            example: 1
        ),
        new OA\Property(
            property: 'status',
            type: 'string',
            example: AttachmentStatus::Completed,
            enum: AttachmentStatus::VALUES,
        ),
        new OA\Property(
            property: 'url',
            type: 'string',
            format: 'uri',
            example: 'https://localhost:8000/storage/reviews/image.jpg',
            nullable: true
        ),
        new OA\Property(
            property: 'original_name',
            type: 'string',
            example: 'image.jpg'
        ),
        new OA\Property(
            property: 'mime_type',
            type: 'string',
            example: 'image/jpeg'
        ),
        new OA\Property(
            property: 'size',
            type: 'integer',
            example: 102400
        ),
    ],
    type: 'object'
)]
class ReviewAttachmentItem {}
