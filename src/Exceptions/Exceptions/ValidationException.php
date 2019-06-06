<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

use JsonSerializable;

/**
 * Class ValidationException
 * @package Napp\Core\Api\Exceptions\Exceptions
 */
class ValidationException extends Exception implements JsonSerializable
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

    /**
     * @var array
     */
    public $validation;

    /**
     * Define the output format
     */
    public function jsonSerialize()
    {
        return [
            'error' => [
                'code' => $this->statusCode,
                'message' => $this->statusMessage,
                'validation' => $this->validation
            ]
        ];
    }
}
