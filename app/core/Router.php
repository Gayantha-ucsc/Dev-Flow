<?php

class Router {
    private array $routes;
    private string $basePath;

    public function __construct() {
        $this->routes = require __DIR__ . '/../../config/routes.php';

        $app = require __DIR__ . '/../../config/app.php';
        $this->basePath = rtrim(parse_url($app['base_url'], PHP_URL_PATH), '/');
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $path = $uri;
        if ($this->basePath !== '' && str_starts_with($path, $this->basePath)) {
            $path = substr($path, strlen($this->basePath));
        }
        $path = '/' . trim($path, '/');

        // Every route needs a logged-in, active user except the ones under 'public' in routes.php.
        if (!in_array($path, $this->routes['public'] ?? [], true)) {
            Middleware::requireAuth();
        }

        $handler = $this->routes[$method][$path] ?? null;

        if (!$handler) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        [$controllerName, $action] = $handler;
        require_once __DIR__ . "/../controllers/{$controllerName}.php";
        (new $controllerName())->$action();
    }
}