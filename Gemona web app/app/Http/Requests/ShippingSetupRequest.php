<?php

namespace App\Http\Requests;

use App\Enums\ShippingMethod;
use Illuminate\Foundation\Http\FormRequest;

class ShippingSetupRequest extends FormRequest
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
            'shipping_setup_method'                 => ['required', 'numeric'],
            'shipping_setup_flat_rate_wise_cost'    => request('shipping_setup_method') == ShippingMethod::FLAT_WISE ? ['required', 'numeric'] : ['nullable', 'numeric'],
            'shipping_setup_area_wise_default_cost' => request('shipping_setup_method') == ShippingMethod::AREA_WISE ? ['required', 'numeric'] : ['nullable', 'numeric'],
        ];
    }
}
