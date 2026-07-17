<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isSuspended()) {
            auth()->logout();

            return redirect()->route('signin')->withErrors(['email' => 'Your account is suspended.']);
        }

        return $next($request);
    }
}
