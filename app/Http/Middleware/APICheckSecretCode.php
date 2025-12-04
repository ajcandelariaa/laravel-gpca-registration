<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class APICheckSecretCode
{
    use HttpResponses;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $api_code = $request->route('api_code');

        if($api_code === env('API_SECRET_CODE')){
            return $next($request);
        } else {
            Log::warning("Unauthorized access attempt with api_code: $api_code from IP: " . $request->ip());
            return $this->error(null, "Unauthorized access", 401);
        }
    }
}
