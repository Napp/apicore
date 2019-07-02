<?php

namespace Napp\Core\Api\Router\Provider;

use Illuminate\Support\ServiceProvider as BaseProvider;

/**
 * Class RouterServiceProvider.
 */
class RouterServiceProvider extends BaseProvider
{
    public function register()
    {
        $this->app->singleton('internalrouter');
    }
}
