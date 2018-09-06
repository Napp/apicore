<?php

namespace Napp\Core\Api\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Napp\Core\Api\Exceptions\Exceptions\ApiInternalCallValidationException;

/**
 * Class ApiHandler
 * @package Napp\Core\Api\Exceptions
 */
class ApiHandler extends ExceptionHandler
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     * @throws \ReflectionException
     */
    public function render($request, Exception $e)
    {
        if (true === app()->isDownForMaintenance()) {
            return response()->json([
                'error' => [
                    'code' => 503,
                    'message' => 'Service is down for scheduled maintenance. Be right back!'
                ]
            ], 503);
        }

        if (true === $e instanceof ApiInternalCallValidationException) {
            $response = response([
                'error' => [
                    'code' => 215,
                    'message' => 'Validation failed']
            ], 400);

            return $response->withException($e);
        }

        return (new NappApiHandler($e))->render();
    }
}
