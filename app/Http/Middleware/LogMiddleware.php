<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Log;

class LogMiddleware
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
        // Setting uuid for every request for tracking purpose
        $request->id = (String) Str::uuid();

        $response = $next($request);

        $log = [
            'id' => $request->id,
            'url' => $request->url(),
            'route' => $request->route() ? $request->route()[1]['as'] : '',
            'req' => $request->all(),
            'res' => $response->getData(),
            'status' => $response->getStatusCode(),
            'timestamp' => \Carbon\Carbon::now()
        ];
        
        // Storing log to /storage/logs/api-[y-m-d].log
        Log::channel('api')->debug(json_encode($log));

        return $response;
    }
}
