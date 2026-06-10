<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressRequest extends FormRequest
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
            'full_name'    => ['required', 'string', 'max:190'],
            'email'        => ['nullable', 'string', 'max:190'],
            'country_code' => ['required', 'string', 'max:28'],
            'phone'        => ['required', 'string', 'max:20'],
            'country'      => ['required', 'string', 'max:100'],
            'state'        => ['required', 'string', 'max:100'],
            'city'         => ['required', 'string', 'max:100'],
            'zip_code'     => ['nullable', 'string'],
            'address'      => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(){
        return [
            'address.required' => 'The street address field is required.'
        ];
    }
}