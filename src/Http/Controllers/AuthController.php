<?php

namespace Api\Http\Controllers;

use Api\Services\AuthService;
use Api\Http\Requests\ForgotPasswordRequest;
use Api\Http\Requests\ResetPasswordRequest;
use Api\Http\Requests\SendVerificationEmailRequest;
use Api\Http\Requests\UserLoginRequest;
use Api\Http\Requests\UserRegisterRequest;
use Api\Http\Requests\VerifyEmailRequest;
use Api\Http\Requests\VerifyResetPasswordRequest;

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
        if (! $user = $this->auth->verifyEmail( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('Email verified successfully');
    }

    public function sendVerificationEmail(SendVerificationEmailRequest $request)
    {
        if (! $this->auth->sendVerificationEmail( $request['email'] )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('Sent verification email successfully');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        if (! $this->auth->sendPasswordResetEmail( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('Sent password reset email successfully');
    }

    public function verifyResetPassword(VerifyResetPasswordRequest $request)
    {
        if (! $user = $this->auth->verifyResetPassword( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendMessageResponse('Reset Token is validated successfully');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        if (! $user = $this->auth->resetPassword( $request->validated() )) {
            return $this->response->errorBadRequest( $this->auth->error() );
        }

        return $this->sendArrayResponse( ['access_token' => $user->access_token] );
    }
}
