<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialRecordResource extends JsonResource
{
    /**
     * Transform the model into a consistent API-ready array.
     *
     * Amount is cast to float with 2 decimal places — consistent regardless
     * of how MySQL returns decimal values.
     *
     * Date is formatted as Y-m-d string — never a datetime, never a timestamp.
     *
     * User is included conditionally with whenLoaded() — avoids N+1 when
     * the relationship has not been eager-loaded.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => round((float) $this->amount, 2),
            'type' => $this->type,
            'category' => $this->category,
            'date' => $this->date->format('Y-m-d'),
            'notes' => $this->notes,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),

            // Only included when relationship is eager-loaded
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
        ];
    }
}