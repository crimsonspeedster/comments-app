<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Review
 */

class ReviewResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'url' => $this->url,
            'text' => $this->text,
        ];
    }
}
