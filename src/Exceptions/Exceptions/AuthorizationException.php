<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

class AuthorizationException extends Exception
{
    /**
     * The suggested HTTP response code.
     *
     * @var int
     */
    public $responseCode = 403;

    /**
     * The suggested status code.
     *
     * @var string
     */
    public $statusCode = 87;

    /**
     * The suggested status message.
     *
     * @var string
     */
    public $statusMessage = 'Authorization error. Requested resource is restricted.';
}