<?php

namespace App\OpenApi\Endpoints;

use OpenApi\Attributes as OA;

#[OA\Post(
    path: '/api/v1/reviews',
    description: 'Create a review with optional file attachments.',
    summary: 'Create review',
    security: [],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                required: ['user_name', 'email', 'text'],
                properties: [
                    new OA\Property(
                        property: 'parent_id',
                        description: 'ID of the parent review.',
                        type: 'integer',
                        example: 1,
                        nullable: true,
                        minimum: 1
                    ),
                    new OA\Property(
                        property: 'user_name',
                        description: 'Author name. Only Latin letters and digits are allowed.',
                        type: 'string',
                        pattern: '^[a-zA-Z0-9]+$',
                        example: 'John123',
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'email',
                        description: 'Author email address.',
                        type: 'string',
                        format: 'email',
                        example: 'john@mail.com',
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'url',
                        description: 'Author website URL.',
                        type: 'string',
                        format: 'uri',
                        example: 'https://example.com',
                        nullable: true,
                        maxLength: 255
                    ),
                    new OA\Property(
                        property: 'text',
                        description: 'Review text.',
                        type: 'string',
                        example: 'This is my review.',
                        maxLength: 10000
                    ),
                    new OA\Property(
                        property: 'attachments',
                        description: 'Optional array of up to 5 files. Allowed formats: JPG, JPEG, PNG, GIF, TXT. Maximum size per file: 100 MB.',
                        type: 'array',
                        items: new OA\Items(
                            type: 'string',
                            format: 'binary'
                        ),
                        maxItems: 5
                    ),
                ],
                type: 'object'
            )
        )
    ),
    tags: ['reviews'],
    responses: [
        new OA\Response(
            response: 201,
            description: 'Review created successfully',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'data',
                        ref: '#/components/schemas/ReviewItem'
                    ),
                ],
                type: 'object'
            )
        ),
        new OA\Response(
            response: 422,
            description: 'Validation error'
        ),
    ]
)]
class ReviewAddEndpoint {}
