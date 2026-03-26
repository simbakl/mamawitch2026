<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('pro')) {
            return redirect()->route('pro.request');
        }

        $proAccount = $user->proAccount;

        if (! $proAccount || $proAccount->status !== 'approved') {
            auth()->logout();
            return redirect()->route('pro.request')->with('error', 'Votre compte pro n\'est pas encore activé.');
        }

        return $next($request);
    }
}
