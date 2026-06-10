<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class ProductCategoryResource extends JsonResource
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
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description === null ? '' : $this->description,
            'parent_category' => optional($this->parent_category)->name,
            'status'          => $this->status,
            'parent_id'       => $this->parent_id,
            'thumb'           => $this->thumb,
            'cover'           => $this->cover
        ];
    }
}
