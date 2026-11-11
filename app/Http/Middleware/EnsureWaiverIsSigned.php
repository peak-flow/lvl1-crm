<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWaiverIsSigned
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()->hasSignedWaiver()) {
            return redirect()
                ->route('waiver.edit')
                ->with('status', 'Please add an emergency contact and sign the waiver first.');
        }

        return $next($request);
    }
}
