<?php

namespace Api\Providers;

use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
    	$this->app->register(AuthServiceProvider::class);
    	$this->app->register(EventServiceProvider::class);
    	$this->app->register(RouteServiceProvider::class);
    }
}
