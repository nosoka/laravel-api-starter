<?php

namespace Api\Http\Controllers;

use Api\Services\AuthService;
use Api\Http\Requests\ReSendVerificationEmailRequest;
use Api\Http\Requests\UserLoginRequest;
use Api\Http\Requests\UserRegisterRequest;
use Api\Http\Requests\VerifyEmailRequest;

class AuthController extends BaseController
{
    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
    }

    public function login(UserLoginRequest $request)
    {
        if (! $user = $this->auth->login( $request->validated() )) {
            return $this->response->errorUnauthorized( $this->auth->error() );
        }

        return $this->sendArrayResponse( ['access_token' => $user->access_token] );
    }

    public function register(UserRegisterRequest $request)
    {
        if (! $newUser = $this->auth->register( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('User registered successfully');
    }

    public function verifyEmail(VerifyEmailRequest $request)
    {
        if (! $this->auth->verifyEmail( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('Email verified successfully');
    }

    public function resendVerificationEmail(ReSendVerificationEmailRequest $request)
    {
        if (! $this->auth->resendVerificationEmail( $request['email'] )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('Sent verification email successfully');
    }
}
