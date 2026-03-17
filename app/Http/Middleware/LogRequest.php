<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\RequestLog;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        RequestLog::create([
            'method' => $request->method(),
            'url'    => $request->fullUrl(),
            'ip'     => $request->ip(),
            'user_id'=> auth()->check() ? auth()->id() : null,
        ]);
        return $next($request);
    }
}
