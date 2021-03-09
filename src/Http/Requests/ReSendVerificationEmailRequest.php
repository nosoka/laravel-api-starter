<?php

namespace Api\Http\Requests;

class ReSendVerificationEmailRequest extends BaseFormRequest
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
