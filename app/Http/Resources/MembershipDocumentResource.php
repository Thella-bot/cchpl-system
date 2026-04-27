<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MembershipDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_type' => $this->document_type,
            'original_name' => $this->original_name,
            // Note: This assumes you have a public disk configured for storage.
            'url' => $this->file_path ? Storage::url($this->file_path) : null,
            'uploaded_at' => $this->created_at->toIso8601String(),
        ];
    }
}