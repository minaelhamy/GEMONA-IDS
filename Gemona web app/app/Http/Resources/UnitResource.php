<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'name_code' => $this->name . ' (' . $this->code . ')',
            'code'      => $this->code,
            'status'    => $this->status,
        ];
    }
}
