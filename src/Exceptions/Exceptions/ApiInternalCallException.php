<?php

namespace Napp\Api\Exceptions\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ApiInternalCallException extends \RuntimeException
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var string
     */
    protected $exceptionMessage;

    /**
     * @param Response $response
     * @param string $message
     */
    public function __construct(Response $response, $message = 'There was an error while processing your request')
    {
        $this->response = $response;
        $this->exceptionMessage = $message;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage;
    }
}
