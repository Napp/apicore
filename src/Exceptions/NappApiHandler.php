<?php

namespace Napp\Api\Exceptions;

use Napp\Api\Exceptions\Exceptions\Exception as NappException;
use Napp\Api\Exceptions\Renderer\DebugRenderer;
use Napp\Api\Exceptions\Renderer\Renderer;
use Napp\Api\Exceptions\Renderer\RendererInterface;
use Illuminate\Http\JsonResponse;

class NappApiHandler
{
    /**
     * @var RendererInterface
     */
    protected $displayer;

    /**
     * @param \Exception $e
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
     * @return int
     */
    protected function getResponseCode(\Exception $e)
    {
        if (true === $e instanceof NappException) {
            /** @var NappException $e */
            return $e->getResponseCode();
        }

        if (true === method_exists($e, 'getStatusCode')) {
            return $e->getStatusCode();
        }

        switch (last(explode('\\', get_class($e)))) {
            case 'ModelNotFoundException':
                return 404;
            default:
                return 500;
        }
    }

    /**
     * @param \Exception $e
     * @return int
     */
    protected function getStatusCode(\Exception $e)
    {
        if (true === $e instanceof NappException) {
            /** @var NappException $e */
            return $e->getStatusCode();
        }

        if ($e->getCode()) {
            return $e->getCode();
        }

        switch ($this->getResponseCode($e)) {
            case 400:
                return 220;
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
     * @return string
     */
    protected function getStatusMessage(\Exception $e)
    {
        if (true === $e instanceof NappException) {
            /** @var NappException $e */
            return $e->getStatusMessage();
        }

        if ($e->getMessage()) {
            return $e->getMessage();
        }

        return (new \ReflectionClass($e))->getShortName();
    }
}
