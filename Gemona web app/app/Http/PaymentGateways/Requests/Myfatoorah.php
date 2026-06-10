<?php

namespace App\Http\PaymentGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Myfatoorah extends FormRequest
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
        if (request()->myfatoorah_status == Activity::ENABLE) {
            return [
                'myfatoorah_api_key' => ['required', 'string'],
                'myfatoorah_mode'    => ['required', 'string'],
                'myfatoorah_status'  => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'myfatoorah_api_key' => ['nullable', 'string'],
                'myfatoorah_mode'    => ['nullable', 'string'],
                'myfatoorah_status'  => ['nullable', 'numeric'],
            ];
        }
    }
}
