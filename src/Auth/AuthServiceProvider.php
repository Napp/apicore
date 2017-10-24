<?php

namespace Napp\Api\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        Auth::extend('api', function ($app, $name, array $config) {
            return new ApiGuard(Auth::createUserProvider($config['provider']), $app['request']);
        });
    }
}
