<?php

class Router {
    private array $routes;
    private string $basePath;

    public static array $params = [];

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
        $params  = [];

        if (!$handler) {
            [$handler, $params] = $this->matchDynamic($method, $path);
        }

        if (!$handler) {
            http_response_code(404);
            require __DIR__ . '/../views/errors/404.php';
            return;
        }

        self::$params = $params;

        [$controllerName, $action] = $handler;
        require_once __DIR__ . "/../controllers/{$controllerName}.php";
        (new $controllerName())->$action();
    }

    private function matchDynamic(string $method, string $path): array {
        $pathSegments = explode('/', trim($path, '/'));

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            if (!str_contains($pattern, ':')) {
                continue;
            }

            $patternSegments = explode('/', trim($pattern, '/'));
            if (count($patternSegments) !== count($pathSegments)) {
                continue;
            }

            $params  = [];
            $matches = true;

            foreach ($patternSegments as $i => $segment) {
                if (str_starts_with($segment, ':')) {
                    $params[substr($segment, 1)] = $pathSegments[$i];
                    continue;
                }
                if ($segment !== $pathSegments[$i]) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                return [$handler, $params];
            }
        }

        return [null, []];
    }
}