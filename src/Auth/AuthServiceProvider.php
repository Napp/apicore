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
        $this->mergeConfigFrom($this->configPath(), 'api-core');

        Auth::extend('api', function ($app, $name, array $config) {
            return new ApiGuard(Auth::createUserProvider($config['provider']), $app['request'], $app['config']->get('api-core.api-key'));
        });
    }

    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('api-core.php'),
        ]);
    }

    /**
     * Get the config path.
     *
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/../../config/api-core.php';
    }
}
