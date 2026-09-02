<?php

declare(strict_types=1);

namespace App\Router;

class Route
{
    private string $method;
    private string $path;
    private $handler;
    private string $pattern;

    public function __construct(string $method, string $path, callable|array $handler)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->handler = $handler;
        $this->pattern = $this->compilePattern($path);
    }

    public function matches(string $method, string $uri, array &$params = []): bool
    {
        if ($this->method !== strtoupper($method)) {
            return false;
        }

        if (preg_match($this->pattern, $uri, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = is_numeric($value) ? (int) $value : $value;
                }
            }
            return true;
        }

        return false;
    }

    public function execute(array $params = []): mixed
    {
        return call_user_func_array($this->handler, $params);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    private function compilePattern(string $path): string
    {
        $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
