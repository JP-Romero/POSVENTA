<?php

namespace App\Core;

class Request
{
    private array $query = [];
    private array $request = [];
    private array $files = [];
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->files = $_FILES;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function get(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->request[$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->request[$key]) || isset($this->query[$key]);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $result[$key] = $this->input($key);
            }
        }
        return $result;
    }

    public function except(array $keys): array
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }
}
