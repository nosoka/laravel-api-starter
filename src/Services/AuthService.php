<?php

namespace Api\Services;

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
            if (!$accessToken = $this->jwtAuth->attempt($credentials)) {
                return $this->setError('Wrong email or password.');
            }
        } catch (JWTException $e) {
            return $this->setError('Wrong email or password.');
        }

        return $accessToken;
    }

    /**
     * @param array
     * @return User model
     * sets errors on failure
     */
    public function register(array $data = [])
    {
        if (!$newUser = $this->user->create($data)) {
            return $this->setError('Could not create user account');
        }

        // TODO:: send verification email
        return $newUser;
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
