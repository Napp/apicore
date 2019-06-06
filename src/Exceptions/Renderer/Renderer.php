<?php

namespace Napp\Core\Api\Exceptions\Renderer;

use Illuminate\Http\JsonResponse;
use Napp\Core\Api\Exceptions\Exceptions\Exception as NappException;

/**
 * Class Renderer
 * @package Napp\Core\Api\Exceptions\Renderer
 */
class Renderer implements RendererInterface
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $statusMessage;

    /**
     * @var int
     */
    protected $responseCode;

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        if (true === $this->exception instanceof NappException) {
            if ($this->exception instanceof \JsonSerializable) {
                return response()->json($this->exception->jsonSerialize(), $this->responseCode);
            }

            return response()->json(
                [
                'error' => [
                    'code' => $this->statusCode,
                    'message' => $this->statusMessage,
                ]],
                $this->responseCode
            );
        }

        switch ($this->responseCode) {
            case 400:
                return response()->json(
                    [
                    'error' => [
                        'code' => $this->statusCode,
                        'message' => 'Unprocessable Entity'
                    ]],
                    $this->responseCode
                );
            case 401:
                return response()->json(
                    [
                        'error' => [
                            'code' => $this->statusCode,
                            'message' => 'Authentication credentials were missing or incorrect'
                        ]],
                    $this->responseCode
                );
            case 403:
                return response()->json(
                    [
                        'error' => [
                            'code' => $this->statusCode,
                            'message' => 'Forbidden'
                        ]],
                    $this->responseCode
                );
            case 404:
                return response()->json(
                    [
                    'error' => [
                        'code' => $this->statusCode,
                        'message' => 'Not Found'
                    ]],
                    $this->responseCode
                );
            case 405:
                return response()->json(
                    [
                    'error' => [
                        'code' => $this->statusCode,
                        'message' => 'Method Not Allowed'
                    ]],
                    $this->responseCode
                );
            default:
                return response()->json(
                    [
                    'error' => [
                        'code' => $this->statusCode,
                        'message' => 'Internal Server Error'
                    ]],
                    500
                );
        }
    }

    /**
     * @param \Exception $e
     * @return void
     */
    public function setException(\Exception $e)
    {
        $this->exception = $e;
    }

    /**
     * @param int $responseCode
     * @return void
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $statusMessage
     * @return void
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }
}
