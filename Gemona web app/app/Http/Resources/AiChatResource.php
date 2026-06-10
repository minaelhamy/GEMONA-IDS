<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AiChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'message'  => $this->message,
            'response' => $this->response
        ];
    }
}
