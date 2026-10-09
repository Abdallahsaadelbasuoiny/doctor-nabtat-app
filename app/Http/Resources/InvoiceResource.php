<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'clinic_id' => $this->clinic_id,
            'patient_id' => $this->patient_id,
            'appointment_id' => $this->appointment_id,
            'amount' => $this->amount,
            'discount' => $this->discount,
            'net_total' => $this->net_total,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at,
        ];
    }
}
