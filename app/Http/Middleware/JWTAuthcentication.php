<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\JWTAuth;

class JWTAuthcentication
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
        try {
          $token = JWTAuth::parseToken()->getPayload();
        } catch (Exception $e) {
          return response('Unauthorized.', 401);
        }


        return $next($request);
    }
}
