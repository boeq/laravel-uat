<?php

declare(strict_types=1);

namespace Boeq\Uat\Http\Middleware;

use Boeq\Uat\Enums\TokenAbility;
use Boeq\Uat\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config()->boolean('uat.api.enabled'), 404);

        $bearerToken = $request->bearerToken();

        abort_if($bearerToken === null, 401, 'A bearer token is required.');

        $token = ApiToken::query()
            ->where('token', hash('sha256', $bearerToken))
            ->first();

        abort_if($token === null || $token->expires_at?->isPast(), 401, 'The bearer token is unknown or expired.');

        $ability = $request->isMethodSafe() ? TokenAbility::Read : TokenAbility::Write;

        abort_unless($token->abilities->contains($ability), 403, 'The bearer token may not '.$ability->value.'.');

        $token->update(['last_used_at' => now()]);

        return $next($request);
    }
}
