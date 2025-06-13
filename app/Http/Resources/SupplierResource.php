<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document' => $this->document,
            'document_formatted' => $this->formatted_document,
            'document_type' => $this->document_type,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile_phone' => $this->mobile_phone,
            'address' => [
                'zip_code' => $this->zip_code,
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'neighborhood' => $this->neighborhood,
                'city' => $this->city,
                'state' => $this->state,
            ],
            'business_info' => $this->when($this->isCnpj(), [
                'cnae_code' => $this->cnae_code,
                'cnae_description' => $this->cnae_description,
                'legal_nature' => $this->legal_nature,
                'opening_date' => $this->opening_date?->format('Y-m-d'),
                'situation' => $this->situation,
            ]),
            'notes' => $this->notes,
            'active' => $this->active,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
