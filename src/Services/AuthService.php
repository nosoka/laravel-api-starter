<?php

namespace Api\Services;

use Api\Events\UserRegistered;
use Api\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class AuthService
{
    protected $error;

    public function __construct(JWTAuth $jwtAuth, User $user)
    {
        $this->jwtAuth  = $jwtAuth;
        $this->user     = $user;
    }

    /**
     * @param array $credentials
     * @return JWT token
     * sets errors on failure
     */
    public function login(array $credentials = [])
    {
        try {
            if (! $this->jwtAuth->attempt($credentials)) {
                return $this->setError( 'Wrong email or password.' );
            }
            if (! auth()->user()->hasVerifiedEmail()) {
                return $this->setError( 'Please verify your email before logging in' );
            }
        } catch (JWTException $e) {
            return $this->setError( 'Wrong email or password.' );
        }

        return auth()->user();
    }

    /**
     * @param array
     * @return User model
     * sets errors on failure
     */
    public function register(array $data = [])
    {
        if (! $newUser = $this->user->create($data)) {
            return $this->setError( 'Could not create user account' );
        }

        event(new UserRegistered($newUser));

        return $newUser;
    }

    public function verifyEmail(array $data = [])
    {
        if (! $user = $this->user->findByIdAndEmailHash($data) ) {
            return $this->setError( 'Could not validate email' );
        }
        if (! $user->markEmailAsVerified() )
            return $this->setError( 'Could not validate email' );

        return true;
    }

    public function resendVerificationEmail(string $email = null)
    {
        if (! $user = $this->user->findByEmail($email) ) {
            return $this->setError( 'No account found with the provided email.' );
        }

        $user->sendEmailVerificationNotification();

        return true;
    }

    public function setError(string $error = null)
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
