<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
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
            'old_password'     => 'required|string|min:6',
            'new_password'     => 'required|string|min:6|required_with:confirm_password',
            'confirm_password' => 'required|string|min:6|same:new_password',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->checkOldPassword()) {
                $validator->errors()->add('old_password', 'The old password does not match.');
            }
        });
    }

    public function checkOldPassword()
    {
        return Hash::check(request('old_password'), auth()->user()->password);
    }

    public function messages(){
        return [
            'confirm_password.same' => 'Password confirmation does not match.'
        ];
    }
}
