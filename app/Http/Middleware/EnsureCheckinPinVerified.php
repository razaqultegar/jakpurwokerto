<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCheckinPinVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('checkin_pin_verified')) {
            return $next($request);
        }

        if ($request->cookie('checkin_device') === hash('sha256', config('checkin.pin'))) {
            $request->session()->put('checkin_pin_verified', true);

            return $next($request);
        }

        $request->session()->put('checkin_intended_url', $request->fullUrl());

        return redirect()->route('checkin.pin');
    }
}
