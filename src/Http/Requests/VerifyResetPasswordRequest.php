<?php

namespace Api\Http\Requests;

class VerifyResetPasswordRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
        ];
    }
}
