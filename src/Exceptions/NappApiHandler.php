<?php

namespace Napp\Core\Api\Exceptions;

use Illuminate\Http\JsonResponse;
use Napp\Core\Api\Exceptions\Exceptions\Exception as NappException;
use Napp\Core\Api\Exceptions\Renderer\DebugRenderer;
use Napp\Core\Api\Exceptions\Renderer\Renderer;
use Napp\Core\Api\Exceptions\Renderer\RendererInterface;

/**
 * Class NappApiHandler.
 */
class NappApiHandler
{
    /**
     * @var RendererInterface
     */
    protected $displayer;

    /**
     * @param \Exception $e
     *
     * @throws \ReflectionException
     */
    public function __construct(\Exception $e)
    {
        if (true === config('app.debug')) {
            $this->displayer = new DebugRenderer();
        } else {
            $this->displayer = new Renderer();
        }

        $this->displayer->setException($e);
        $this->displayer->setResponseCode($this->getResponseCode($e));
        $this->displayer->setStatusCode($this->getStatusCode($e));
        $this->displayer->setStatusMessage($this->getStatusMessage($e));
    }

    /**
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return $this->displayer->render();
    }

    /**
     * @param \Exception $e
     *
     * @return int
     */
    protected function getResponseCode(\Exception $e): ?int
    {
        if (true === $e instanceof NappException) {
            /* @var NappException $e */
            return $e->getResponseCode();
        }

        if (true === \method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        if (\property_exists($e, 'status')) {
            return $e->status;
        }

        switch (last(explode('\\', \get_class($e)))) {
            case 'ModelNotFoundException':
                return 404;
            default:
                return 500;
        }
    }

    /**
     * @param \Exception $e
     *
     * @return int
     */
    protected function getStatusCode(\Exception $e): ?int
    {
        if (true === $e instanceof NappException) {
            /* @var NappException $e */
            return $e->getStatusCode();
        }

        if ($e->getCode()) {
            return $e->getCode();
        }

        switch ($this->getResponseCode($e)) {
            case 400:
                return 220;
            case 401:
                return 135;
            case 403:
                return 64;
            case 404:
                return 34;
            case 405:
                return 34;
            default:
                return 500;
        }
    }

    /**
     * @param \Exception $e
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    protected function getStatusMessage(\Exception $e): string
    {
        if (true === $e instanceof NappException) {
            /* @var NappException $e */
            return $e->getStatusMessage();
        }

        if ($e->getMessage()) {
            return $e->getMessage();
        }

        return (new \ReflectionClass($e))->getShortName();
    }
}
