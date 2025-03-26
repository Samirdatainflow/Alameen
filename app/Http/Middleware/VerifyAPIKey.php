<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAPIKey
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
        // You can check API key database too
        // $generate_api_key = bin2hex(openssl_random_pseudo_bytes(8));
        $api_keys = array('44b48f2305bf2680', 'a40d97bfc2ab0e56');
        if ($request->header('Authorization')) {
            $api_key = $request->header('Authorization');

            // check token
            if (in_array($api_key, $api_keys)) {
                return $next($request);
            } else {
                return response()->json([
                    'results' => 'API key is not valid',
                ]);
            }
        }

        return response()->json([
            'results' => 'Authorization failed',
        ]);
    }
}
