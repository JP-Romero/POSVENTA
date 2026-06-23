<?php

namespace App;

use App\Core\Router;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\AppException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Helpers\Session;

class Application
{
    private Router $router;
    private Request $request;

    public function __construct()
    {
        Session::start();
        $this->router = new Router();
        $this->request = new Request();
        
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        // Register your routes here or load from routes file
        $routesFile = dirname(__DIR__) . '/routes/web.php';
        if (file_exists($routesFile)) {
            require_once $routesFile;
        }
    }

    public function run(): void
    {
        try {
            $uri = $this->request->uri();
            $method = $this->request->method();

            $this->router->dispatch($uri, $method);
        } catch (ValidationException $e) {
            Response::json([
                'success' => false,
                'errors' => $e->getErrors(),
                'message' => $e->getMessage()
            ], $e->getStatusCode())->send();
        } catch (NotFoundException $e) {
            http_response_code(404);
            $this->renderErrorPage('404', $e->getMessage());
        } catch (AppException $e) {
            Response::json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode())->send();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    protected function handleException(\Throwable $e): void
    {
        $debug = getenv('APP_ENV') !== 'production';

        if ($debug) {
            echo "<h1>Error: " . htmlspecialchars($e->getMessage()) . "</h1>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            error_log($e->getMessage());
            $this->renderErrorPage('500', 'Error interno del servidor');
        }
    }

    protected function renderErrorPage(string $code, string $message): void
    {
        http_response_code((int)$code);
        
        $viewPath = dirname(__DIR__) . '/app/views/errors/' . $code . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "<h1>Error {$code}</h1><p>{$message}</p>";
        }
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
