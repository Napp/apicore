<?php

namespace Napp\Core\Api\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Napp\Core\Api\Exceptions\Exceptions\ApiInternalCallValidationException;

/**
 * Class ApiHandler.
 */
class ApiHandler extends ExceptionHandler
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @throws \ReflectionException
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $e)
    {
        if (true === app()->isDownForMaintenance()) {
            return response()->json([
                'error' => [
                    'code'    => 503,
                    'message' => 'Service is down for scheduled maintenance. Be right back!',
                ],
            ], 503);
        }

        if (true === $e instanceof ApiInternalCallValidationException) {
            $response = response([
                'error' => [
                    'code'    => 215,
                    'message' => 'Validation failed', ],
            ], 400);

            return $response->withException($e);
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => [
                    'code'    => 64,
                    'message' => 'Forbidden',
                ],
            ], 403);
        }

        return (new NappApiHandler($e))->render();
    }
}
