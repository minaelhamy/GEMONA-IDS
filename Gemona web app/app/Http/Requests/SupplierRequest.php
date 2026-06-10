<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
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
            'company'      => ['required', 'string', 'max:190', Rule::unique("suppliers", "company")->ignore($this->route('supplier.id'))],
            'name'         => ['required', 'string', 'max:190', Rule::unique("suppliers", "name")->ignore($this->route('supplier.id'))],
            'email'        => ['nullable', 'email', 'max:190', Rule::unique("suppliers", "email")->ignore($this->route('supplier.id'))],
            'phone'        => ['nullable', 'string', 'max:20', Rule::unique("suppliers", "phone")->ignore($this->route('supplier.id'))],
            'address'      => ['nullable', 'string', 'max:500'],
            'country'      => ['nullable', 'string', 'max:200'],
            'state'        => ['nullable', 'string', 'max:200'],
            'city'         => ['nullable', 'string', 'max:200'],
            'zip_code'     => ['nullable', 'string', 'max:200'],
            'country_code' => ['nullable', 'string', 'max:20'],
            'image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048']
        ];
    }
}