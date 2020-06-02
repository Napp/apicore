<?php

namespace Napp\Core\Api\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Napp\Core\Api\Exceptions\Exceptions\ApiInternalCallValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ApiHandler.
 */
class ApiHandler extends ExceptionHandler
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception|\Throwable $e
     *
     * @throws \ReflectionException
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, $e)
    {
        if (true === app()->isDownForMaintenance()) {
            return response()->json([
                'error' => [
                    'code'    => 503,
                    'message' => 'Service is down for scheduled maintenance. Be right back!',
                ],
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if ($e instanceof NotFoundHttpException) {
            return response()->json(
                [
                    'code' => 34,
                    'message' => 'Not Found',
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        if ($e instanceof ValidationException) {
            return response()->json(
                [
                    'code' => 215,
                    'message' => 'Validation failed',
                    'errors' => $e->validator->errors()->toArray(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if ($e instanceof AuthenticationException) {
            return response()->json([
                'error' => [
                    'code'    => 64,
                    'message' => 'Forbidden',
                ],
            ], Response::HTTP_FORBIDDEN);
        }

        if ($e instanceof ApiInternalCallValidationException) {
            $response = response([
                'error' => [
                    'code'    => 215,
                    'message' => 'Validation failed',
                ],
            ], 400);

            return $response->withException($e);
        }

        return (new NappApiHandler($e))->render();
    }
}
