<?php

namespace Napp\Core\Api\Router\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Napp\Core\Api\Router\Router
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
