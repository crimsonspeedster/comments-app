<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class CommentSearchService
{
    public function __construct(
        private Client $client,
    ) {}

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function indexComment(array $data): array
    {
        $response = $this->client->index([
            'index' => config('elasticsearch.indices.comments'),
            'id' => (string) $data['id'],
            'body' => [
                'id' => $data['id'],
                'parent_id' => $data['parent_id'] ?? null,
                'user_name' => $data['user_name'],
                'email' => $data['email'],
                'url' => $data['url'] ?? null,
                'text' => $data['text'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
            ],
        ]);

        return $response->asArray();
    }

    public function createIndex(): array
    {
        $response = $this->client->indices()->create([
            'index' => config('elasticsearch.indices.comments'),
            'body' => [
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'long'],
                        'parent_id' => ['type' => 'long'],
                        'user_name' => ['type' => 'keyword'],
                        'email' => ['type' => 'keyword'],
                        'url' => ['type' => 'keyword'],
                        'text' => ['type' => 'text'],
                        'created_at' => ['type' => 'date'],
                        'updated_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);

        return $response->asArray();
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function deleteIndex(): array
    {
        $response = $this->client->indices()->delete([
            'index' => config('elasticsearch.indices.comments'),
        ]);

        return $response->asArray();
    }

    public function clearIndex(): array
    {
        $response = $this->client->deleteByQuery([
            'index' => config('elasticsearch.indices.comments'),
            'body' => [
                'query' => [
                    'match_all' => (object) [],
                ],
            ],
        ]);

        return $response->asArray();
    }

    public function searchReviews(
        int $perPage,
        int $page,
        string $sortBy = 'created_at',
        string $sortDirection = 'desc',
        ?int $parentId = null,
    ): array {
        $allowedSortFields = [
            'created_at',
            'updated_at',
            'user_name',
        ];

        if (!in_array($sortBy, $allowedSortFields, true)) {
            $sortBy = 'created_at';
        }

        $sortDirection = strtolower($sortDirection) === 'asc'
            ? 'asc'
            : 'desc';

        $query = [
            'bool' => [
                'filter' => [
                    [
                        'term' => [
                            'parent_id' => $parentId,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->client->search([
            'index' => config('elasticsearch.indices.comments'),
            'body' => [
                'query' => $query,
                'sort' => [
                    [
                        $sortBy => [
                            'order' => $sortDirection,
                        ],
                    ],
                    [
                        'id' => [
                            'order' => 'desc',
                        ],
                    ],
                ],
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
            ],
        ]);

        return $response->asArray();
    }
}
