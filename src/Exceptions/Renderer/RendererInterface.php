<?php

namespace Napp\Core\Api\Exceptions\Renderer;

use Illuminate\Http\JsonResponse;

/**
 * Interface RendererInterface.
 */
interface RendererInterface
{
    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse;

    /**
     * @param \Throwable $e
     *
     * @return void
     */
    public function setException(\Throwable $e);

    /**
     * @param int $responseCode
     *
     * @return void
     */
    public function setResponseCode($responseCode);

    /**
     * @param int $statusCode
     *
     * @return void
     */
    public function setStatusCode($statusCode);

    /**
     * @param string $statusMessage
     *
     * @return void
     */
    public function setStatusMessage($statusMessage);
}
