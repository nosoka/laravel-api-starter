<?php

namespace Api\Http\Requests;

class ForgotPasswordRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|exists:users,email',
        ];
    }
}
