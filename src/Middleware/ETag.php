<?php

namespace Napp\Core\Api\Middleware;

use Closure;

class ETag
{
    /**
     * Implement ETag support.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if (true === $request->isMethod('get')) {
            $etag = md5($response->getContent());
            $requestEtag = str_replace('"', '', $request->getETags());

            if ($requestEtag && $requestEtag[0] == $etag) {
                $response->setNotModified();
            }

            $response->setEtag($etag);
        }

        return $response;
    }
}
