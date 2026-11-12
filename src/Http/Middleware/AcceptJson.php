<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcceptJson
{
    public function handle(Request $request, Closure $next): Response
    {
        // Agents rarely send an Accept header, and without it Laravel answers errors with HTML or a redirect.
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
