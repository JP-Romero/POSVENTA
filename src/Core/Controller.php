<?php

namespace App\Core;

use App\Exceptions\NotFoundException;

class Controller
{
    protected array $data = [];
    protected string $layout = 'main';

    public function __construct()
    {
        $this->data = [];
    }

    protected function model(string $modelClass): object
    {
        if (!class_exists($modelClass)) {
            throw new \RuntimeException("Model {$modelClass} not found");
        }
        return new $modelClass();
    }

    protected function view(string $view, array $data = []): void
    {
        extract($data);
        
        $viewPath = dirname(__DIR__, 2) . '/app/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new NotFoundException("View '{$view}' not found");
        }
    }

    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: " . URLROOT . '/' . ltrim($url, '/'));
        exit;
    }

    protected function setLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }
}
