<?php

namespace Napp\Core\Api\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

/**
 * Class AuthServiceProvider.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        Auth::extend('api', function ($app, $name, array $config) {
            return new ApiGuard(Auth::createUserProvider($config['provider']), $app['request'], $app['config']->get('api-core.api-key'));
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/api-core.php' => config_path('api-core.php'),
        ]);
    }
}
