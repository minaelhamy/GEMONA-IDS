<?php

namespace App\Http\Requests;

use App\Models\OrderArea;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderAreaRequest extends FormRequest
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
            'country'       => ['required', 'string', 'max:900'],
            'state'         => ['required', 'string', 'max:900', Rule::unique("order_areas", "state")->ignore($this->route('orderArea.id'))->where('city', request('city'))],
            'city'          => ['required', 'string', 'max:900', Rule::unique("order_areas", "city")->ignore($this->route('orderArea.id'))->where('state', request('state'))],
            'shipping_cost' => ['required', 'string', 'max:900'],
            'status'        => ['required', 'numeric', 'max:24'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $country = OrderArea::where('country', $this->country)->where('state', $this->state)->where('city', $this->city)->whereNot('id', $this->route('orderArea.id'))->first();
            if ($country) {
                $validator->getMessageBag()->add('country', trans('all.message.country_exist'));
            }
        });
    }
}