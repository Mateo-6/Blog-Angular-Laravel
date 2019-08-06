<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
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

        $jwtAuth = new \JwtAuth();

        $token = $request->header('Authorization');
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken) {

            return $next($request);

        } else {

            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'User is not indentified'
            );

            return response()->json($data, $data['code']);
        }

    }
}
