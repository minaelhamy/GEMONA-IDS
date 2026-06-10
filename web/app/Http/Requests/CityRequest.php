<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
                Rule::unique("cities", "name")
                ->where(function($query) {
                    $query->where('state_id', $this->input('state'));
                })
                ->ignore($this->route('city.id'))
            ],
            'state'          => ['required', 'numeric'],
            'status'           => ['required', 'numeric']
        ];
    }

}
