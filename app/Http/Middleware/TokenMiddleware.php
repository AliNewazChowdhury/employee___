<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use Closure;

class TokenMiddleware
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
        if (!$request->header('authorization')) {
            return response(['error' => 'Unauthorized Access'], 401);
        }

        $authToken =  $request->header('authorization');
        $accessUserId = $request->header('accessUserId');
        $redisKey = 'red_access_token_' . $accessUserId;
        $storedToken = Cache::has($redisKey) ? Cache::get($redisKey) : '';

        if (!empty($storedToken)) {
            if ($storedToken == $authToken) {
                return $next($request);
            } else {
                if ($this->guzzleTest($authToken)) {
                    Cache::put($redisKey, $authToken, 28800);
                    return $next($request);
                }
            }
        } else {
            if ($this->guzzleTest($authToken)) {
                Cache::put($redisKey, $authToken, 28800);
                return $next($request);
            }
        }

        return response(['error' => 'Unauthorized Access'], 401);
    }

    private function guzzleTest($token)
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'http://auth-service.local/token-verification', [
                'headers' => [
                    'Authorization' => $token
                ]
            ]);
        } catch (\Exception $ex) {
            return false;
        }

        if (isset($response) && $response->getStatusCode() == 200) {
            return true;
        }

        return false;
    }
}
