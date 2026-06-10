<?php

namespace App\Http\SocialProviders\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Google extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        if (request()->google_status == Activity::ENABLE) {
            return [
                'google_client_id'     => ['required', 'string'],
                'google_client_secret' => ['required', 'string'],
                'google_status'        => ['nullable', 'numeric'],
            ];
        } else {
            return [
                'google_client_id'     => ['nullable', 'string'],
                'google_client_secret' => ['nullable', 'string'],
                'google_status'        => ['nullable', 'numeric'],
            ];
        }
    }
}
