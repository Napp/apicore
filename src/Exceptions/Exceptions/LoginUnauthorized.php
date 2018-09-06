<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

/**
 * Class LoginUnauthorized
 * @package Napp\Core\Api\Exceptions\Exceptions
 */
class LoginUnauthorized extends Exception
{
    /**
     * The suggested HTTP response code.
     *
     * @var int
     */
    public $responseCode = 401;

    /**
     * The suggested status code.
     *
     * @var string
     */
    public $statusCode = 215;

    /**
     * The suggested status message.
     *
     * @var string
     */
    public $statusMessage = 'User unauthorized';
}
