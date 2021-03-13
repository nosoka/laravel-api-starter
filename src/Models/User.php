<?php

namespace Api\Models;

use Api\Notifications\ResetPassword;
use Api\Notifications\VerificationEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;

// Extend instead of changing the user model that comes with laravel install
class_alias(config('auth.providers.users.model'), 'Api\Models\DynamicParent');

class User extends DynamicParent implements JWTSubject, MustVerifyEmail, CanResetPassword
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


    public function token()
    {
        return $this->hasOne(PasswordReset::class, 'email', 'email');
    }

    public function getAccessTokenAttribute()
    {
        return JWTAuth::fromUser($this) ?: false;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerificationEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        return $this->notify(new ResetPassword($token));
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
