<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductAttributeOptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:190',
                Rule::unique("product_attribute_options", "name")->ignore(
                    $this->route('productAttributeOption.id')
                )->where('product_attribute_id', $this->route('productAttribute.id'))
            ]
        ];
    }
}
