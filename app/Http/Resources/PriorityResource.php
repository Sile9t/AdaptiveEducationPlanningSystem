<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriorityResource extends JsonResource
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
            'full_name' => $this->full_name,
            'category' => $this->category,
            'position' => $this->position,
            'branch' => $this->branch,
            'permit' => $this->permit,
            'passed_at' => $this->passed_at,
            'expired_at' => $this->expired_at,
            'status' => $this->status,
        ];
    }
}
