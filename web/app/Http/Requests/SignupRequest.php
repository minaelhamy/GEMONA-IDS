<?php

namespace App\Http\Requests;


use App\Enums\Ask;
use App\Models\User;
use App\Enums\Status;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:255'],
            'country'           => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'email', 'max:255', Rule::unique("users", "email")->where('is_guest', Ask::NO)],
            'phone'             => ['required', 'string', 'max:20'],
            'country_code'      => ['required', 'string', 'max:10'],
            'organization_mode' => ['required', 'string', Rule::in(['existing', 'new'])],
            'organization_id'   => [
                'required_if:organization_mode,existing',
                'nullable',
                Rule::exists('organizations', 'id')->where('status', Status::ACTIVE),
            ],
            'organization_name' => ['required_if:organization_mode,new', 'nullable', 'string', 'max:255'],
            'address'           => ['required', 'string', 'max:1000'],
            'password'          => ['required', 'string', 'min:6'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!blank(request('phone')) && !blank(request('country_code'))) {
                $checkUser = User::where(['country_code' => request('country_code'), 'phone' => request('phone')])->where('is_guest', Ask::NO)->first();
                if ($checkUser) {
                    $validator->errors()->add('phone', 'The phone has already been taken.');
                }
            }
        });
    }
}
