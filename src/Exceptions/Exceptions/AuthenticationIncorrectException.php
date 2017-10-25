<?php 

namespace Napp\Core\Api\Exceptions\Exceptions;

class AuthenticationIncorrectException extends Exception
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
    public $statusCode = 135;

    /**
     * The suggested status message.
     *
     * @var string
     */
    public $statusMessage = 'Authentication credentials were missing or incorrect';
}
