<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BenefitRequest extends FormRequest
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
            'title'        => [
                'required',
                'string',
                'max:190',
                Rule::unique("benefits", "title")->ignore($this->route('benefit.id'))
            ],
            'description' => ['required', 'string', 'max:900'],
            'status'      => ['required', 'numeric', 'max:24'],
            'image'       => $this->route('benefit.id') ? ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'] : ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
