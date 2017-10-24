<?php

namespace Napp\Api\Router\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;
use Napp\Api\Router\Router;

class LaravelServiceProvider extends BaseProvider {

    public function register()
    {

    }

    public function boot()
    {
        $this->app->singleton('internalrouter', function(){
            $app = app();

            return new Router($app, $app['request'], $app['router']);
        });
    }

}