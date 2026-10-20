<?php

namespace App\Http\Resources;

use App\Enums\AttachmentStatus;
use App\Models\ReviewAttachment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin ReviewAttachment
 */

class ReviewAttachmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'url' => $this->status === AttachmentStatus::Completed && $this->processed_path
                ? Storage::disk('public')->url($this->processed_path)
                : null,
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
        ];
    }
}
