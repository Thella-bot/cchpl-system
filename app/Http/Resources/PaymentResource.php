<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'receipt_number' => $this->receipt_number,
            'amount' => (float) $this->amount,
            'provider' => $this->provider,
            'transaction_reference' => $this->transaction_reference,
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
            'verified_at' => $this->verified_at ? $this->verified_at->toIso8601String() : null,
            // Conditionally load the user for efficiency
            'member' => new UserResource($this->whenLoaded('membership', fn() => $this->membership->user)),
        ];
    }
}