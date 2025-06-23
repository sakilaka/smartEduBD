<?php

namespace App\Http\Middleware;

use Closure;

class CheckContentType
{
    public function handle($request, Closure $next)
    {
        $requestFrom = $request->header('Request-From') ?? 'app';
        app()->bind('request_from', function () use ($requestFrom) {
            return $requestFrom;
        });

        $headerType = $request->header('Content-Type');
        $method = $request->method();

        if (in_array($method, ['GET', 'DELETE'])) {
            return $next($request);
        } else if (in_array($method, ['POST', 'PUT']) && in_array($headerType, ['application/x-www-form-urlencoded', 'application/json'])) {
            return $next($request);
        } else if (in_array($method, ['POST', 'PUT']) && strpos($headerType, 'multipart/form-data') !== false) {
            return $next($request);
        }

        throw new \Exception('Bad request');
    }
}
