<?php

namespace Api\Models;

use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

// Extend instead of changing the user model that comes with laravel install
class_alias(config('auth.providers.users.model'), 'Api\Models\DynamicParent');

class User extends DynamicParent implements JWTSubject
{
    protected $hidden     = [
        'password', 'remember_token'
    ];
    protected $fillable   = [
        'name', 'email', 'password'
    ];
    protected $appends = [
        'access_token'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAccessTokenAttribute()
    {
        return JWTAuth::fromUser($this) ?: false;
    }

   public function create(array $data = [])
    {
        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make($data['password']);
        }

        return parent::create($data) ?: false;
    }
}
