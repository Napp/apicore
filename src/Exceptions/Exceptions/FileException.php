<?php

namespace Napp\Api\Exceptions\Exceptions;

use Exception;

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
