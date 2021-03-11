<?php

namespace Api\Services;

use Api\Events\UserRegistered;
use Api\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthService
{
    protected $error;

    public function __construct(JWTAuth $jwtAuth, User $user, PasswordBroker $passwordBroker)
    {
        $this->jwtAuth          = $jwtAuth;
        $this->user             = $user;
        $this->passwordBroker   = $passwordBroker;
    }

    /**
     * @param array $credentials
     * @return JWT token
     * sets errors on failure
     */
    public function login(array $credentials)
    {
        if (! $this->jwtAuth->attempt($credentials)) {
            return $this->setError( 'Wrong email or password.' );
        }

        if (! auth()->user()->hasVerifiedEmail()) {
            return $this->setError( 'Please verify your email before logging in' );
        }

        return auth()->user();
    }

    /**
     * @param array
     * @return User model
     * sets errors on failure
     */
    public function register(array $data)
    {
        if (! $newUser = $this->user->create($data)) {
            return $this->setError( 'Could not create user account' );
        }

        event(new UserRegistered($newUser));

        return $newUser;
    }

    public function verifyEmail(array $data)
    {
        if (! $user = $this->user->findByIdAndEmailHash($data) ) {
            return $this->setError( 'Could not validate email' );
        }
        if (! $user->markEmailAsVerified() )
            return $this->setError( 'Could not validate email' );

        return $user;
    }

    public function sendVerificationEmail(string $email)
    {
        if (! $user = $this->user->findByEmail($email) ) {
            return $this->setError( 'No account found with the provided email.' );
        }

        $user->sendEmailVerificationNotification();

        return true;
    }

    public function sendPasswordResetEmail(array $credentials)
    {
        if (! $user = $this->user->findByEmail($credentials['email']) ) {
            return $this->setError('Could not find that account. Please crosscheck and retry.');
        }

        if (! $token = $this->passwordBroker->createToken($user) ) {
            return $this->setError('Failed to create password reset code. Please contact support');
        }

        $user->sendPasswordResetNotification($token);

        return true;
    }

    public function verifyResetPassword(array $data)
    {
        if (! $user = $this->user->findByEmail($data['email']) ) {
            return $this->setError('Could not find that account. Please crosscheck and retry.');
        }
        if (! $this->passwordBroker->tokenExists($user, $data['token']) ) {
            return $this->setError('Could not find that token. Please crosscheck and retry.');
        }

        return $user;
    }


    public function resetPassword(array $data)
    {
        if (! $user = $this->verifyResetPassword($data)) {
            return false;
        }

        $user->update(['password' => $data['password']]);
        $this->passwordBroker->deleteToken($user);

        return $user;
    }

    public function setError(string $error)
    {
        $this->error = $error;

        return false;
    }

    public function getError()
    {
        return $this->error;
    }

    public function error()
    {
        return $this->error;
    }
}
