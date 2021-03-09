<?php

namespace Api\Listeners;

use Api\Events\UserRegistered;
use Log;

class SendVerificationEmail
{
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        Log::info("Sending Verification email to userid - {$event->user->getKey()}");
        $event->user->sendEmailVerificationNotification();
    }
}
