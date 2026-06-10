<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Libraries\AppLibrary;
use App\Models\ProductVariation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'product_variation_id' => ['nullable', 'numeric'],
            'attribute'            => ['required', 'json']
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $status     = false;
            $message    = "";
            $variations = json_decode($this->attribute);


            if (is_array($variations) && count($variations)) {
                foreach ($variations as $variation) {
                    if ($status) {
                        break;
                    }

                    $price           = AppLibrary::amountCheck($variation->price);
                    $checkProductSku = Product::where(['sku' => $variation->sku])->first();
                    if ($this->route('productVariation.id')) {
                        $checkVariationSku = ProductVariation::where('sku', $variation->sku)->where('id', '!=', $this->route('productVariation.id'))->first();
                    } else {
                        $checkVariationSku = ProductVariation::where('sku', $variation->sku)->first();
                    }

                    if(empty($variation->price)){
                        $status  = true;
                        $message = trans('all.message.the_price_field_is_required');
                    }
                    elseif (!$price->status) {
                        $status  = true;
                        $message = trans('all.message.price_invalid');
                    } elseif (!is_int((int)$variation->product_attribute_id)) {
                        $status  = true;
                        $message = trans('all.message.product_attribute_required');
                    } elseif (!is_int((int)$variation->product_attribute_option_id)) {
                        $status  = true;
                        $message = trans('all.message.product_attribute_option_invalid');
                    } elseif (blank($variation->sku)) {
                        $status  = true;
                        $message = trans('all.message.variation_sku_required');
                    } elseif ($checkVariationSku || $checkProductSku) {
                        $status  = true;
                        $message = trans('all.message.sku_exist');
                    }
                }
            } else {
                $status  = true;
                $message = trans('all.message.attribute_invalid');
            }

            if ($status) {
                $validator->errors()->add('global', $message);
            }
        });
    }
}
