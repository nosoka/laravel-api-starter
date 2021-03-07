<?php

namespace Api\Http\Requests;

class UserRegisterRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'     => 'required|email|max:255|unique:users',
            'password'  => 'required|min:4',
            'name'      => 'required|max:255',
        ];
    }
}
