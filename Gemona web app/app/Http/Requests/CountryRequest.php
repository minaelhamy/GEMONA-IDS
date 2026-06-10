<?php

namespace App\Http\Requests;

use App\Enums\DiscountType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CountryRequest extends FormRequest
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
            'name'        => [
                'required',
                'string',
                'max:190',
                Rule::unique("countries", "name")->ignore($this->route('country.id'))
            ],
            'code'             => ['required', 'string', 'max:2', Rule::unique("countries", "code")->ignore($this->route('country.id'))],
            'status'           => ['required', 'numeric']
        ];
    }

}
