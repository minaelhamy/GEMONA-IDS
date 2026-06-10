<?php

namespace App\Http\Requests;

use App\Rules\IniAmount;
use Illuminate\Foundation\Http\FormRequest;

class ProductOfferRequest extends FormRequest
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
    public function rules()
    {
        return [
            'add_to_flash_sale' => ['required', 'numeric', 'max:24'],
            'discount'          => ['required', 'numeric', 'max:100', new IniAmount()],
            'offer_start_date'  => ['required', 'string', 'max:190'],
            'offer_end_date'    => ['required', 'string', 'max:190'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->isNotNull(request('offer_start_date'))) {
                $validator->errors()->add('offer_start_date', 'The start date field is required');
            }

            if (!$this->isNotNull(request('offer_end_date'))) {
                $validator->errors()->add('offer_end_date', 'The end date field is required');
            }

            if ($this->isNotNull(request('offer_start_date')) && strtotime(request('offer_end_date')) < strtotime(request('offer_start_date'))) {
                $validator->errors()->add('offer_end_date', 'To date can\'t be older than Start date.');
            }
            if ($this->isNotNull(request('offer_start_date')) && $this->checkToDate()) {
                $validator->errors()->add('offer_end_date', 'To date can\'t be older than now.');
            }
        });
    }


    private function checkToDate()
    {
        $today = strtotime(date('Y-m-d H:i:s'));
        if (strtotime(request('offer_end_date')) < $today) {
            return true;
        }
    }

    private function isNotNull($value)
    {
        if ($value === 'null') {
            return false;
        }
        return true;
    }
}