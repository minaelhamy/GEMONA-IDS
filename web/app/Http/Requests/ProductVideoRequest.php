<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVideoRequest extends FormRequest
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

            'video_provider' => ['required', 'numeric', 'max:24'],
            'link'           => ['required', 'url', 'max:5000',
                Rule::unique("product_videos", "link")->where('product_id', $this->route('product.id'))->ignore($this->route('productVideo.id'))
            ],
        ];
    }
}
