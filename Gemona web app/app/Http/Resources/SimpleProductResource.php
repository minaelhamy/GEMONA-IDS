<?php

namespace App\Http\Resources;

use App\Enums\Ask;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $price = count($this->variations) > 0 ? $this->variation_price : $this->selling_price;
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'currency_price'    => AppLibrary::currencyAmountFormat($price),
            'cover'             => $this->cover,
            'flash_sale'        => $this->add_to_flash_sale == Ask::YES,
            'is_offer'          => AppLibrary::isBetweenDate($this->offer_start_date, $this->offer_end_date),
            'discounted_price'  => AppLibrary::currencyAmountFormat($price - (($price / 100) * $this->discount)),
            'rating_star'       => $this->rating_star,
            'rating_star_count' => (int) $this->rating_star_count,
            'wishlist'          => (bool)$this->wishlist,
        ];
    }
}
