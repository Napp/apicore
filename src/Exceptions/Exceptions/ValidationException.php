<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

/**
 * Class ValidationException
 * @package Napp\Core\Api\Exceptions\Exceptions
 */
class ValidationException extends Exception
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
    public $statusCode = 215;

    /**
     * The suggested status message.
     *
     * @var string
     */
    public $statusMessage = 'Validation failed';
}
