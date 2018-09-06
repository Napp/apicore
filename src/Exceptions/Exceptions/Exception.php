<?php 

namespace Napp\Core\Api\Exceptions\Exceptions;

use Exception as BaseException;

/**
 * Class Exception
 * @package Napp\Core\Api\Exceptions\Exceptions
 */
class Exception extends BaseException
{
    /**
     * The suggested HTTP response code.
     *
     * @var int
     */
    public $responseCode;

    /**
     * The suggested status code.
     *
     * @var int
     */
    public $statusCode;

    /**
     * The suggested status message.
     *
     * @var string
     */
    public $statusMessage;

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }
}
