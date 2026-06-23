<?php

namespace App\Middleware;

use App\Core\Response;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle($request, callable $next)
    {
        // Check if user is admin
        if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 1) {
            return Response::json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
