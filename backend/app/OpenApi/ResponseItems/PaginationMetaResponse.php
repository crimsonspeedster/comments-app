<?php
namespace App\OpenApi\ResponseItems;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaginationMetaResponse",
    properties: [
        new OA\Property(
            property: 'current_page',
            type: 'integer',
            example: 1
        ),
        new OA\Property(
            property: 'from',
            type: 'integer',
            example: 1,
            nullable: true
        ),
        new OA\Property(
            property: 'last_page',
            type: 'integer',
            example: 5
        ),
        new OA\Property(
            property: 'path',
            type: 'string',
            format: 'uri',
            example: 'https://localhost:8000/api/v1/reviews'
        ),
        new OA\Property(
            property: 'per_page',
            type: 'integer',
            example: 25
        ),
        new OA\Property(
            property: 'to',
            type: 'integer',
            example: 25,
            nullable: true
        ),
        new OA\Property(
            property: 'total',
            type: 'integer',
            example: 125
        ),
    ],
    type: "object"
)]
class PaginationMetaResponse {}
