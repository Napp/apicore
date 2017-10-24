<?php

namespace Napp\Api\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends BaseController
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var int
     */
    protected $responseCode = 200;

    /**
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setResponseCode($code)
    {
        $this->responseCode = $code;

        return $this;
    }

    /**
     * @param string $currentETag
     * @return JsonResponse
     */
    public function checkETag($currentETag)
    {
        $newETag = request('ETag');

        if (null !== $newETag && $newETag === $currentETag) {
            return $this->responseNotModified(['ETag' => $currentETag]);
        }
    }

    /**
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function respond(array $data, array $headers = []): JsonResponse
    {
        return Response::json($data, $this->getResponseCode(), $headers);
    }

    /**
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function respondWithSingleObject(array $data, array $headers = []): JsonResponse
    {
        return Response::json(reset($data), $this->getResponseCode(), $headers);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function respondWithError(string $message): JsonResponse
    {
        return $this->respond([
            'error' => [
                'message' => $message,
                'code'    => $this->getStatusCode()
            ]
        ]);
    }

    /**
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function responseCreated(array $data, array $headers = []): JsonResponse
    {
        return $this->setResponseCode(201)
            ->respond($data, $headers);
    }

    /**
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    public function responseNoContent(
        string $message = 'The request was successful, but no content was send back.',
        array $headers = []
    ): JsonResponse {
        return $this->setResponseCode(204)
            ->respond(['message' => $message], $headers);
    }

    /**
     * @param array $headers
     * @return JsonResponse
     */
    public function responseNotModified(array $headers = []): JsonResponse
    {
        return $this->setResponseCode(304)
            ->respond(null, $headers);
    }

    /**
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseBadRequest(string $message = 'The request failed due to an application error.'): JsonResponse
    {
        return $this->setStatusCode(215)
            ->setResponseCode(400)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseValidationFailed(string $message = 'Validation error.'): JsonResponse
    {
        return $this->setStatusCode(215)
            ->setResponseCode(400)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseUnprocessableEntity(string $message = 'The request was well-formed but was unable to be followed due to semantic errors.'): JsonResponse
    {
        return $this->setStatusCode(220)
            ->setResponseCode(422)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseUnauthorized(string $message = 'Authentication credentials were missing or incorrect.'): JsonResponse
    {
        return $this->setStatusCode(135)
            ->setResponseCode(401)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseForbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return $this->setStatusCode(64)
            ->setResponseCode(403)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseNotFound(string $message = 'Not found.'): JsonResponse
    {
        return $this->setStatusCode(34)
            ->setResponseCode(404)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseClientNotFound(string $message = 'Client not found.'): JsonResponse
    {
        return $this->setStatusCode(35)
            ->setResponseCode(404)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseInternalServerError(string $message = 'Internal Server Error.'): JsonResponse
    {
        return $this->setStatusCode(131)
            ->setResponseCode(500)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseNotImplemented(string $message = 'The request has not been implemented.'): JsonResponse
    {
        return $this->setStatusCode(131)
            ->setResponseCode(501)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseServiceUnavailable(string $message = 'Service Unavailable.'): JsonResponse
    {
        return $this->setStatusCode(131)
            ->setResponseCode(503)
            ->respondWithError($message);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function responseHTTPVersionNotSupported(string $message = 'This is returned if you use the HTTP protocol.'): JsonResponse
    {
        return $this->setStatusCode(251)
            ->setResponseCode(505)
            ->respondWithError($message);
    }

    /**
     * @param Request $request
     * @return int
     */
    public function getLimit(Request $request): int
    {
        $limit = (int) $request->get('limit', 30);

        if ($limit > 200) {
            $limit = 200;
        }

        return $limit;
    }

    /**
     * @param Request $request
     * @return int
     */
    protected function getOffset(Request $request): int
    {
        return (int) $request->get('offset', 0);
    }
}
