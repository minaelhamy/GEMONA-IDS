<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProductSectionProductRequest extends FormRequest
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

            'product_id'   => [
                'required',
                'numeric',
                Rule::unique("product_section_products", "product_id")->ignore($this->route('productSectionProducts.id'))->where('product_section_id', $this->route('productSection.id')),
            ],
        ];
    }

    public function messages(){
        return [
            "product_id.required" => "The product field is required."
        ];
    }
}
