<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedWhenRequired
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            config('auth.require_email_verification', true)
            && $request->user() instanceof MustVerifyEmail
            && ! $request->user()->hasVerifiedEmail()
        ) {
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
