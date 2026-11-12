<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Middleware;

use Boeq\Uat\Contracts\Access\ResolvesTester;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeTester
{
    public function __construct(
        protected ResolvesTester $tester,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_if($this->tester->resolve($request->user()) === null, 404);

        return $next($request);
    }
}
