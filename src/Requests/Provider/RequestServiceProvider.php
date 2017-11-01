<?php

namespace Napp\Core\Api\Requests\Provider;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Napp\Core\Api\Auth\NappHttpHeaders;

class RequestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (false === Request::hasMacro('isApiInternalCall')) {
            Request::macro('isApiInternalCall', function () {
                return $this->header(NappHttpHeaders::NAPP_API_CALL_TYPE) === 'internal';
            });
        }
    }

    public function register()
    {
    }
}
