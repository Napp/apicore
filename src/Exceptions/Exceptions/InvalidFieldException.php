<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

class InvalidFieldException extends Exception
{
    /**
     * The suggested HTTP response code.
     *
     * @var int
     */
    public $responseCode = 400;

    /**
     * The suggested status code.
     *
     * @var string
     */
    public $statusCode = 221;

    /**
     * The suggested status message.
     *
     * @var string
     */
    public $statusMessage = 'Invalid field detected';
}
