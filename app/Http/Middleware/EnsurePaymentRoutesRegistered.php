<?php

namespace App\Http\Middleware;

use App\Settings\InstanceSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePaymentRoutesRegistered
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app(InstanceSettings::class)->is_payment_enabled) {
            return $next($request);
        }

        return redirect()->route('dashboard');
    }
}
