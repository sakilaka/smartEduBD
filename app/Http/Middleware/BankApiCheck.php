<?php

namespace App\Http\Middleware;

use Closure;

class BankApiCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $access_token = $request->header('ACCESS-TOKEN');
        $secret = $request->header('SECRET');

        app()->bind('request_from', function () {
            return 'dbank';
        });

        // check whitelisting ip
        $whitelist = config('app.bank_api_whitelist_ip');
        if (config('app.bank_api') == 'live' && !in_array($ip, $whitelist)) {
            throw new \Exception('Bad request');
        }

        // check token and secret
        if (config('app.bank_api_access_token') === $access_token && config('app.bank_api_secret') === $secret) {
            return $next($request);
        }

        throw new \Exception('Bad request');
    }
}