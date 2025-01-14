<?php

namespace App\Http\Middleware;

use App\Settings\InstanceSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (InstanceSettings::isSelfHosted()) {
            return $next($request);
        }

        if (blank(auth()->user()->paid())) {
            return redirect()->route('billing');
        }

        return $next($request);
    }
}
