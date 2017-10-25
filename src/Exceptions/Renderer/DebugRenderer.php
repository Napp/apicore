<?php

namespace Napp\Core\Api\Exceptions\Renderer;

use Illuminate\Http\JsonResponse;

class DebugRenderer implements RendererInterface
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
        return response()->json(
            [
            'error' => [
                'code' => $this->statusCode,
                'message' => $this->statusMessage,
                'type' => get_class($this->exception),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->formatTrace($this->exception->getTrace())
            ]],
            $this->responseCode
        );
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

    /**
     * Remove the args property from the trace array objects.
     *
     * @param array $trace
     * @return array
     */
    protected function formatTrace(array $trace)
    {
        foreach ($trace as &$t) {
            $t = array_except($t, ['args']);
        }

        return $trace;
    }
}
