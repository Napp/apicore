<?php

namespace Napp\Core\Api\Exceptions\Exceptions;

use Exception;

class NoFileException extends Exception
{
    /**
     * @var string
     */
    protected $exceptionMessage = 'Please select a file.';

    /**
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }
}
