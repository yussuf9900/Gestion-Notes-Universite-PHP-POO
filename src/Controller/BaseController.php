<?php

declare(strict_types=1);

namespace App\Controller;

abstract class BaseController
{
    protected int $statusCode = 200;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    protected function render(string $view, array $data = [], int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        if (!headers_sent()) {
            http_response_code($statusCode);
        }
        extract($data);

        $baseDir = dirname(__DIR__, 2) . '/templates/';
        require $baseDir . 'layout/header.php';
        require $baseDir . $view . '.php';
        require $baseDir . 'layout/footer.php';
    }

    protected function redirect(string $url, int $statusCode = 302): void
    {
        $this->statusCode = $statusCode;
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Location: ' . $url);
        }
        if (php_sapi_name() !== 'cli') {
            exit;
        }
    }
}
