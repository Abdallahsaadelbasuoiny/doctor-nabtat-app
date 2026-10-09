<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'amount' => $this->amount,
            'method' => $this->method,
            'paid_at' => $this->paid_at,
            'received_by_user_id' => $this->received_by_user_id,
        ];
    }
}
