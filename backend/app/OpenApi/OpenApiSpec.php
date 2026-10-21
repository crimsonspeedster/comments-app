<?php
namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "API documentation for project",
    title: "API for project",
)]
#[OA\Server(
    url: "https://localhost:8000/",
    description: "Api server"
)]
class OpenApiSpec {}
