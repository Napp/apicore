<?php

namespace Napp\Core\Api\Router\Provider;

use Napp\Core\Api\Router\Router;
use Illuminate\Support\ServiceProvider as BaseProvider;

/**
 * Class RouterServiceProvider.
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
