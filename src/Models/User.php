<?php

namespace Api\Models;

use Api\Notifications\VerificationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

// Extend instead of changing the user model that comes with laravel install
class_alias(config('auth.providers.users.model'), 'Api\Models\DynamicParent');

class User extends DynamicParent implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

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

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificationEmail);
    }

    public function findByLogin(string $login = null)
    {
        return $this->where('email', $login)->first() ?: false;
    }

    public function findByEmail(string $email = null)
    {
        return $this->where('email', $email)->first() ?: false;
    }

    public function findByIdAndEmailHash(array $data = [])
    {
        if (! $row = $this->find($data['id'])) {
            return false;
        }

        if (! hash_equals($data['hash'], sha1($row->email))) {
            return false;
        }

        return $row;
    }
}
