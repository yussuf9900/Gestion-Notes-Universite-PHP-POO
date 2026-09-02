<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Controller\CopieExamenController;
use App\Repository\Database;
use App\Repository\PdoCopieExamenRepository;
use App\Router\Router;
use App\Service\CalculNoteAvecRetardService;
use App\Service\SoumissionCopieService;

\Dotenv\Dotenv::createImmutable(dirname(__DIR__))->safeLoad();

$pdo = Database::getConnection();
$repository = new PdoCopieExamenRepository($pdo);
$calculService = new CalculNoteAvecRetardService();
$service = new SoumissionCopieService($repository, $calculService);
$controller = new CopieExamenController($service);

$router = new Router();

$router->get('/copies', [$controller, 'index']);
$router->get('/copies/create', [$controller, 'create']);
$router->post('/copies', [$controller, 'store']);
$router->get('/copies/{id}', function (int $id) use ($controller) {
    $controller->show($id);
});

$router->get('/', function () {
    header('Location: /copies');
    exit;
});

$router->setNotFoundHandler(function () {
    http_response_code(404);
    $baseDir = dirname(__DIR__) . '/templates/';
    require $baseDir . 'layout/header.php';
    require $baseDir . 'errors/404.php';
    require $baseDir . 'layout/footer.php';
});

$router->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    $_SERVER['REQUEST_URI'] ?? '/copies'
);
