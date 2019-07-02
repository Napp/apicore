<?php

namespace Napp\Core\Api\Controllers;

use Illuminate\Http\Response;
use Illuminate\Auth\AuthManager;
use Napp\Core\Api\Router\Router;
use Illuminate\Http\JsonResponse;
use Napp\Core\Api\Auth\NappHttpHeaders;
use Napp\Core\Api\Exceptions\Exceptions\ApiInternalCallException;

/**
 * Class ApiInternalController.
 */
class ApiInternalController extends BaseController
{
    /**
     * @var AuthManager
     */
    protected $auth;

    /**
     * @var Router
     */
    protected $internalApiRouter;

    /**
     * @param AuthManager $auth
     * @param Router      $router
     */
    public function __construct(AuthManager $auth, Router $router)
    {
        $this->auth = $auth;
        $this->internalApiRouter = $router;
    }

    /**
     * @return Router
     */
    public function getInternalRouter(): Router
    {
        return $this->internalApiRouter;
    }

    /**
     * @param string $uri
     * @param array  $headers
     *
     * @return array
     */
    public function get(string $uri, array $headers = []): array
    {
        return $this->formatResponse(
            $this->getInternalRouter()->get($uri, [], $this->getInternalCallHeaders($headers))
        );
    }

    /**
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     *
     * @return array
     */
    public function post(string $uri, array $data, array $headers = []): array
    {
        return $this->formatResponse(
            $this->getInternalRouter()->post($uri, $data, $this->getInternalCallHeaders($headers))
        );
    }

    /**
     * @param string $uri
     * @param array  $data
     * @param array  $headers
     *
     * @return array
     */
    public function put(string $uri, array $data, array $headers = []): array
    {
        return $this->formatResponse(
            $this->getInternalRouter()->put($uri, $data, $this->getInternalCallHeaders($headers))
        );
    }

    /**
     * @param string $uri
     * @param array  $headers
     *
     * @return array
     */
    public function delete(string $uri, array $headers = []): array
    {
        return $this->formatResponse(
            $this->getInternalRouter()->delete($uri, [], $this->getInternalCallHeaders($headers))
        );
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function getInternalCallHeaders(array $headers): array
    {
        return array_merge($headers, [
            NappHttpHeaders::NAPP_API_CALL_TYPE       => 'internal',
            NappHttpHeaders::NAPP_AUTH_GLOBAL_USER_ID => $this->auth->guard()->id(),
        ]);
    }

    /**
     * @param Response|JsonResponse $response
     *
     * @throws ApiInternalCallException
     *
     * @return array
     */
    protected function formatResponse($response): array
    {
        if (true === $response instanceof JsonResponse) {
            $data = $response->getData(true);
        } else {
            $data = json_decode($response->getContent(), true);
        }

        if (true === array_key_exists('error', $data)) {
            if (true === config('app.debug')) {
                $message = json_encode($data['error']);
            } else {
                $message = $data['error']['message'];
            }

            throw new ApiInternalCallException($response, $message);
        }

        return $data;
    }
}
