<?php

return [
    'host' => env('ELASTICSEARCH_HOST', 'https://localhost:9200'),
    'username' => env('ELASTICSEARCH_USERNAME', 'elastic'),
    'password' => env('ELASTICSEARCH_PASSWORD'),
    'ca_cert' => env('ELASTICSEARCH_CA_CERT'),
    'indices' => [
        'comments' => env('ELASTICSEARCH_COMMENTS_INDEX', 'comments'),
    ],
];
