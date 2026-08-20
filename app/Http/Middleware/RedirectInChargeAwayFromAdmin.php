<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectInChargeAwayFromAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'in_charge' && $request->path() === 'admin') {
            if ($user->unit) {
                return redirect()->route('public.roster.unit', $user->unit->id);
            }

            return redirect()->route('public.roster');
        }

        return $next($request);
    }
}
