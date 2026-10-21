<?php
namespace App\OpenApi\ResponseItems;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaginationLinksResponse",
    properties: [
        new OA\Property(
            property: 'first',
            type: 'string',
            format: 'uri',
            nullable: true
        ),
        new OA\Property(
            property: 'last',
            type: 'string',
            format: 'uri',
            nullable: true
        ),
        new OA\Property(
            property: 'prev',
            type: 'string',
            format: 'uri',
            nullable: true
        ),
        new OA\Property(
            property: 'next',
            type: 'string',
            format: 'uri',
            nullable: true
        ),
    ],
    type: "object"
)]
class PaginationLinksResponse {}
