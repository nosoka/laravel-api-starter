<?php

namespace Api\Http\Requests;

class UserLoginRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'     => 'required',
            'password'  => 'required',
        ];
    }
}
