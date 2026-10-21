<?php

namespace App\OpenApi\ResponseItems;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ReviewPaginationResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(
                ref: '#/components/schemas/ReviewItem'
            ),
        ),
        new OA\Property(
            property: 'links',
            ref: '#/components/schemas/PaginationLinksResponse'
        ),
        new OA\Property(
            property: 'meta',
            ref: '#/components/schemas/PaginationMetaResponse'
        ),
    ],
    type: 'object'
)]
class ReviewPaginationResponse {}
