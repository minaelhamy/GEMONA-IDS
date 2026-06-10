<?php

namespace App\Http\Requests;

use App\Enums\ShippingType;
use App\Rules\IniAmount;
use Illuminate\Foundation\Http\FormRequest;

class ShippingAndReturnRequest extends FormRequest
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
    public function rules()
    {
        return [
            'shipping_type'                => ['required', 'numeric', 'max_digits:10'],
            'shipping_cost'                => request('shipping_type') == ShippingType::FLAT_RATE ? ['required', new IniAmount()] : ['nullable'],
            'is_product_quantity_multiply' => request('shipping_type') == ShippingType::FLAT_RATE ? ['required', 'numeric', 'max_digits:10'] : ['nullable', 'numeric', 'max_digits:10'],
            'shipping_and_return'          => ['nullable', 'string', 'max:8000']
        ];
    }
}
