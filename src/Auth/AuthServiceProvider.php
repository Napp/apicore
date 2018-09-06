<?php

namespace Napp\Core\Api\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

/**
 * Class AuthServiceProvider
 * @package Napp\Core\Api\Auth
 */
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
