<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
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
                Rule::unique("promotions", "name")->ignore($this->route('promotion.id'))
            ],
            'type'     => ['required', 'numeric', 'max:24'],
            'status'     => ['required', 'numeric', 'max:24'],
            'image'      => $this->route('promotion.id') ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'] : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}