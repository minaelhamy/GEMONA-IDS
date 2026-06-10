<?php

namespace App\Http\AiAgents\Requests;

use App\Enums\Activity;
use Illuminate\Foundation\Http\FormRequest;

class Openai extends FormRequest
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
        if (request()->openai_status == Activity::ENABLE) {
            return [
                'openai_api_key'         => ['required', 'string'],
                'openai_status'          => ['required', 'numeric'],
            ];
        } else {
            return [
                'openai_api_key'         => ['nullable', 'string'],
                'openai_status'          => ['required', 'numeric'],
            ];
        }
    }
}
