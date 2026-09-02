<?php

declare(strict_types=1);

namespace App\Router;

class Router
{
    private array $routes = [];
    private $notFoundHandler = null;

    public function get(string $path, callable|array $handler): self
    {
        return $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): self
    {
        return $this->addRoute('POST', $path, $handler);
    }

    public function addRoute(string $method, string $path, callable|array $handler): self
    {
        $this->routes[] = new Route($method, $path, $handler);
        return $this;
    }

    public function setNotFoundHandler(callable|array $handler): self
    {
        $this->notFoundHandler = $handler;
        return $this;
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        foreach ($this->routes as $route) {
            $params = [];
            if ($route->matches($method, $path, $params)) {
                return $route->execute($params);
            }
        }

        if ($this->notFoundHandler !== null) {
            return call_user_func($this->notFoundHandler);
        }

        if (!headers_sent()) {
            http_response_code(404);
        }
        echo '404 Not Found';
        return null;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
