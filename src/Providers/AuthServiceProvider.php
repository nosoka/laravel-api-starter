<?php

namespace Api\Providers;

use Api\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->changeAuthProviderModel();
    }

    /**
     * Change the default Auth Service Providers users model
     * instead of changing the user model
     *
     * @return void
     */
    public function changeAuthProviderModel()
    {
        // To avioid changing the user model that comes with laravel install
        return auth()->getProvider()->setModel(User::class);
    }
}
