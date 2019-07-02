<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

/**
 * Class ActionNotAllowedException.
 */
class ActionNotAllowedException extends Exception
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
    public $statusMessage = 'You are not authorized to perform this action';
}
