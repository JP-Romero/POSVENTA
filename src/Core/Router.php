<?php

namespace App\Core;

use App\Exceptions\NotFoundException;
use App\Config\Config;

class Router
{
    protected array $routes = [];
    protected array $middlewareGroups = [];
    
    public function get(string $path, string|array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string|array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function put(string $path, string|array $handler): self
    {
        return $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string|array $handler): self
    {
        return $this->addRoute('DELETE', $path, $handler);
    }

    protected function addRoute(string $method, string $path, string|array $handler): self
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->normalizePath($path),
            'handler' => $handler,
            'middleware' => []
        ];
        return $this;
    }

    public function middleware(array $middleware, string $group = 'default'): self
    {
        $this->middlewareGroups[$group] = $middleware;
        return $this;
    }

    protected function normalizePath(string $path): string
    {
        $path = trim($path, '/');
        return '/' . ($path ?: '');
    }

    public function dispatch(string $uri, string $method): void
    {
        $uri = '/' . trim($uri, '/');
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($this->matchRoute($route['path'], $uri, $params)) {
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }

        throw new NotFoundException("Route not found: {$method} {$uri}");
    }

    protected function matchRoute(string $routePath, string $uri, array &$params): bool
    {
        if ($routePath === $uri) {
            $params = [];
            return true;
        }

        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    protected function executeHandler(string|array $handler, array $params): void
    {
        if (is_string($handler)) {
            [$controller, $method] = explode('@', $handler);
            
            if (!class_exists($controller)) {
                throw new NotFoundException("Controller {$controller} not found");
            }

            $instance = new $controller();
            
            if (!method_exists($instance, $method)) {
                throw new NotFoundException("Method {$method} not found in {$controller}");
            }

            call_user_func_array([$instance, $method], $params);
        } else {
            call_user_func($handler);
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
