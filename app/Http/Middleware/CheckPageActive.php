<?php

namespace App\Http\Middleware;

use App\Filament\Pages\PageManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPageActive
{
    /**
     * Check if the requested page is active. Abort 404 if disabled.
     */
    public function handle(Request $request, Closure $next, string $pageSlug): Response
    {
        if (! PageManager::isPageActive($pageSlug)) {
            abort(404);
        }

        return $next($request);
    }
}
