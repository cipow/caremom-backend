<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use JWT;

class JWTAuthcentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
          $token = JWT::decode($request->get('token'), env('JWT_SECRET'), ['HS256']);

          if ($token->aud == $guard)
            return $next($request);
          else
            throw new Exception("Error Processing Request", 1);

        } catch (Exception $e) {
          return response('Unauthorized.', 401);
        }

    }
}
