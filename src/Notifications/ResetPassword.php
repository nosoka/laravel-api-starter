<?php

namespace Api\Notifications;;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends BaseResetPassword implements ShouldQueue
{
    use Queueable;

    public static $createUrlCallback = [self::class, 'createUrl'];

    public static function createUrl($notifiable, $token)
    {
        $route = 'api.auth.reset.password';

        return url(route($route, [
            'token' => $token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]));
    }
}
