<?php

namespace Napp\Core\Api\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiProxyController extends ApiInternalController
{
    /**
     * @param string $endpoint
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(string $endpoint, Request $request): JsonResponse
    {
        $response = $this->callApi($endpoint, $request);

        return response()->json($response);
    }

    /**
     * @param string $endpoint
     * @param Request $request
     * @return mixed
     */
    private function callApi(string $endpoint, Request $request)
    {
        $requestMethod = strtolower($request->getMethod());
        $arguments = $request->all();
        $url = "/api/{$endpoint}";

        $methodSupportsArguments = in_array($requestMethod, ['post', 'put', 'patch']);
        $callArguments = [$url];

        if (true === $methodSupportsArguments) {
            $callArguments = [$url, $arguments];

            return $this->{$requestMethod}(...$callArguments);
        }

        if (false === empty($arguments)) {
            $callArguments = [$url . '?' . http_build_query($arguments)];
        }

        return $this->{$requestMethod}(...$callArguments);
    }
}
