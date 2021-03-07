<?php

namespace Api\Http\Controllers;

use Api\Services\AuthService;
use Api\Http\Requests\UserLoginRequest;
use Api\Http\Requests\UserRegisterRequest;

class AuthController extends BaseController
{
    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function login(UserLoginRequest $request)
    {
        if (!$access_token = $this->auth->login( $request->validated() )) {
            return $this->response->errorUnauthorized( $this->auth->error() );
        }

        return $this->sendArrayResponse( compact('access_token') );
    }

    public function register(UserRegisterRequest $request)
    {
        if (!$newUser = $this->auth->register( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('User registered successfully');
    }
}
