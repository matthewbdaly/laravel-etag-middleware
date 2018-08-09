<?php

namespace Matthewbdaly\ETagMiddleware;

use Closure;
use Illuminate\Http\Request;

/**
 * ETag middleware.
 */
class ETag
{
    /**
     * Implement Etag support.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @param \Closure                 $next    Closure for the response.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If this was not a get or head request, just return
        if (!$request->isMethod('get') && !$request->isMethod('head')) {
            return $next($request);
        }
        $initialMethod = $request->method();
        $request->setMethod('get'); // force to get in order to recieve content

        // Get response
        $response = $next($request);
        // Generate Etag
        $etag = md5($response->getContent());
        $requestEtag = str_replace('"', '', $request->getETags());
        // Check to see if Etag has changed
        if ($requestEtag && $requestEtag[0] == $etag) {
            $response->setNotModified();
        }
        // Set Etag
        $response->setEtag($etag);

        $request->setMethod($initialMethod); // set back to original method
        // Send response
        return $response;
    }
}
