<?php

namespace Napp\Api\Router\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Napp\Api\Router\Router
 */
class InternalRouter extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'internalrouter';
    }
}