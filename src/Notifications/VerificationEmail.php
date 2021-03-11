<?php

namespace Api\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationEmail extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    protected function verificationUrl($notifiable)
    {
        $route          = 'api.auth.verify.email';
        $expirationTime = now()->addMinutes(config('auth.verification.expire', 10));

        return app('api.url')->version('v1')->temporarySignedRoute(
            $route, $expirationTime,
            [
                'id'    => $notifiable->getKey(),
                'hash'  => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
