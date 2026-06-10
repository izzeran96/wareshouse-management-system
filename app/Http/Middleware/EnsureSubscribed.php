<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the application behind an active subscription (SaaS access).
 *
 * Super Admins always pass. Everyone else is redirected to the subscribe
 * page until they hold an active, unexpired subscription.
 */
class EnsureSubscribed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->hasActiveSubscription()) {
            // Avoid a redirect loop on the subscribe pages themselves.
            if (! $request->routeIs('subscribe.*')) {
                if ($request->hasHeader('X-Livewire')) {
                    abort(redirect()->route('subscribe.index'));
                }

                return redirect()->route('subscribe.index');
            }
        }

        return $next($request);
    }
}
