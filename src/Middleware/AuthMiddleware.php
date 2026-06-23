<?php

namespace App\Middleware;

use App\Core\Response;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle($request, callable $next)
    {
        // Check if user is authenticated
        if (!isset($_SESSION['user_id'])) {
            return Response::json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
