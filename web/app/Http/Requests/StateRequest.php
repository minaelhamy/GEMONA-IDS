<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StateRequest extends FormRequest
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
                Rule::unique("states", "name")
                ->where(function($query) {
                    $query->where('country_id', $this->input('country'));
                })
                ->ignore($this->route('state.id'))
            ],
            'country'          => ['required', 'numeric'],
            'status'           => ['required', 'numeric']
        ];
    }

}
