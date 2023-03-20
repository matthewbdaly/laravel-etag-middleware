<?php

namespace Matthewbdaly\ETagMiddleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * ETag middleware.
 */
class ETag
{
    /**
     * Implement Etag support.
     *
     * @param Request $request The HTTP request.
     * @param Closure $next    Closure for the response.
     *
     * @psalm-param Closure(Request): Response $next    Closure for the response.
     *
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        // If this was not a get or head request, just return
        if (!$request->isMethod('get') && !$request->isMethod('head')) {
            return $next($request);
        }

        // Get the initial method sent by client
        $initialMethod = $request->method();

        // Force to get in order to receive content
        $request->setMethod('get');

        // Get response
        /** @var Response $response */
        $response = $next($request);

        // Generate Etag
        $etag = md5(json_encode($response->headers->get('origin')).(string) $response->getContent());

        // Load the Etag sent by client
        $requestEtag = str_replace('"', '', $request->getETags());

        // Check to see if Etag has changed
        if ($requestEtag && $requestEtag[0] == $etag) {
            $response->setNotModified();
        }

        // Set Etag
        $response->setEtag($etag);

        // Set back to original method
        $request->setMethod($initialMethod); // set back to original method

        // Send response
        return $response;
    }
}
