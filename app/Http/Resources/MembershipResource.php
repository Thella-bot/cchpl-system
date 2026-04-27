<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'member_id' => $this->member_id,
            'status' => $this->status,
            'expiry_date' => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : null,
            'created_at' => $this->created_at->toIso8601String(),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new MembershipCategoryResource($this->whenLoaded('category')),
            
            // These relationships are only loaded on the 'show' endpoint
            'documents' => MembershipDocumentResource::collection($this->whenLoaded('documents')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}