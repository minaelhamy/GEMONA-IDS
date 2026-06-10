<?php

namespace App\Http\PaymentGateways\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Easypaisa extends FormRequest
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
        if (request()->easypaisa_status == Activity::ENABLE) {
            return [
                'easypaisa_store_id'   => ['required', 'string'],
                'easypaisa_hash_key'   => ['required', 'string'],
                'easypaisa_username'   => ['required', 'string'],
                'easypaisa_password'   => ['required', 'string'],
                'easypaisa_mode'       => ['required', 'string'],
                'easypaisa_status'     => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'easypaisa_store_id'   => ['nullable', 'string'],
                'easypaisa_hash_key'   => ['nullable', 'string'],
                'easypaisa_username'   => ['nullable', 'string'],
                'easypaisa_password'   => ['nullable', 'string'],
                'easypaisa_mode'       => ['nullable', 'string'],
                'easypaisa_status'     => ['nullable', 'numeric'],
            ];
        }
    }
}