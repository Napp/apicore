<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

use Exception;

/**
 * Class FileException.
 */
class FileException extends Exception
{
    /**
     * @var string
     */
    protected $exceptionMessage = 'File is corrupted.';

    /**
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }
}
