<?php

namespace Api\Events;

use Api\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Log;

class UserRegistered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        Log::info("New user registered - userid - {$user->getKey()}");
    }
}
