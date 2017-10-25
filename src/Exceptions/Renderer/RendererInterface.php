<?php

namespace Napp\Core\Api\Exceptions\Renderer;

use Illuminate\Http\JsonResponse;

interface RendererInterface
{
    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse;

    /**
     * @param \Exception $e
     * @return void
     */
    public function setException(\Exception $e);

    /**
     * @param int $responseCode
     * @return void
     */
    public function setResponseCode($responseCode);

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode($statusCode);

    /**
     * @param string $statusMessage
     * @return void
     */
    public function setStatusMessage($statusMessage);
}
