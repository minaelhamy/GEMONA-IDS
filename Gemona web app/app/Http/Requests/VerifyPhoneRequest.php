<?php

namespace App\Http\Requests;


use App\Enums\Ask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyPhoneRequest extends FormRequest
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
            'country_code' => ['required', 'string', 'max:20'],
            'phone'        => ['required', 'string', 'max:180'],
            'token'        => ['required', 'max:180'],
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'The code field is required'
        ];
    }
}
