<?php

namespace Napp\Core\Api\Router\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Napp\Core\Api\Router\Router;

/**
 * Class RouterServiceProvider
 * @package Napp\Core\Api\Router\Provider
 */
class RouterServiceProvider extends BaseProvider
{
    public function register()
    {
    }

    public function boot()
    {
        $this->app->singleton('internalrouter', function () {
            $app = app();

            return new Router($app, $app['request'], $app['router']);
        });
    }
}
