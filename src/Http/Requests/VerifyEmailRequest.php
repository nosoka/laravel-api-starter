<?php

namespace Api\Http\Requests;

class VerifyEmailRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'expires'       => 'required',
            'hash'          => 'required',
            'id'            => 'required|exists:users,id',
            'signature'     => 'required',
        ];
    }
}
