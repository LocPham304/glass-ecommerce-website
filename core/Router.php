<?php

namespace Core;

use RuntimeException;

class Router
{
    protected array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $uri, string|callable $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, string|callable $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = $this->normalizePath($uri);
        $action = $this->routes[$method][$uri] ?? null;

        if ($action === null) {
            http_response_code(404);
            require BASE_PATH . '/app/views/errors/404.php';
            return;
        }

        if (is_callable($action)) {
            $action();
            return;
        }

        [$controllerName, $controllerMethod] = explode('@', $action);
        $controllerClass = 'App\\Controllers\\' . $controllerName;

        if (!class_exists($controllerClass)) {
            throw new RuntimeException("Controller {$controllerClass} không tồn tại.");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $controllerMethod)) {
            throw new RuntimeException("Method {$controllerMethod} không tồn tại trong {$controllerClass}.");
        }

        $controller->{$controllerMethod}();
    }

    protected function addRoute(string $method, string $uri, string|callable $action): void
    {
        $this->routes[$method][$this->normalizePath($uri)] = $action;
    }

    protected function normalizePath(string $uri): string
    {
        $cleanPath = '/' . trim($uri, '/');

        return $cleanPath === '//' ? '/' : $cleanPath;
    }
}
