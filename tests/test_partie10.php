<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Router\Route;
use App\Router\Router;

$passed = 0;
$failed = 0;

function assertCondition(bool $condition, string $testName): void
{
    global $passed, $failed;
    if ($condition) {
        echo "\033[32m[PASS]\033[0m {$testName}\n";
        $passed++;
    } else {
        echo "\033[31m[FAIL]\033[0m {$testName}\n";
        $failed++;
    }
}

echo "=== Suite de Tests — Partie 10 : Routeur HTTP ===\n\n";

try {
    $route = new Route('GET', '/copies/{id}', function (int $id) {
        return "copie_{$id}";
    });

    assertCondition($route->getMethod() === 'GET', "Route conserve la methode HTTP GET");
    assertCondition($route->getPath() === '/copies/{id}', "Route conserve le chemin declare");

    $params = [];
    $matched = $route->matches('GET', '/copies/42', $params);
    assertCondition($matched === true, "La route /copies/{id} matche avec l'URI /copies/42");
    assertCondition(isset($params['id']) && $params['id'] === 42, "Extraction automatique du parametre dynamique 'id' sous forme d'entier (42)");

    $result = $route->execute($params);
    assertCondition($result === 'copie_42', "Route::execute() execute le callback avec les parametres extraits");

    $notMatchedMethod = $route->matches('POST', '/copies/42', $params);
    assertCondition($notMatchedMethod === false, "Rejet si la methode HTTP ne correspond pas (POST != GET)");

    $notMatchedPath = $route->matches('GET', '/copies/42/sub', $params);
    assertCondition($notMatchedPath === false, "Rejet si le chemin comporte des segments supplementaires non prevus");

    $router = new Router();
    $router->get('/copies', function () {
        return 'liste_des_copies';
    });
    $router->get('/copies/create', function () {
        return 'formulaire_de_soumission';
    });
    $router->post('/copies', function () {
        return 'traitement_soumission';
    });
    $router->get('/copies/{id}', function (int $id) {
        return 'detail_copie_' . $id;
    });

    $routes = $router->getRoutes();
    assertCondition(count($routes) === 4, "Enregistrement des 4 routes demandees dans le Routeur");

    $res1 = $router->dispatch('GET', '/copies');
    assertCondition($res1 === 'liste_des_copies', "GET /copies resout vers l'affichage de la liste");

    $res2 = $router->dispatch('GET', '/copies/create');
    assertCondition($res2 === 'formulaire_de_soumission', "GET /copies/create resout vers le formulaire de creation");

    $res3 = $router->dispatch('POST', '/copies');
    assertCondition($res3 === 'traitement_soumission', "POST /copies resout vers le traitement de la soumission");

    $res4 = $router->dispatch('GET', '/copies/15');
    assertCondition($res4 === 'detail_copie_15', "GET /copies/{id} resout vers le detail avec le parametre extrait (ID 15)");

    $notFoundTriggered = false;
    $router->setNotFoundHandler(function () use (&$notFoundTriggered) {
        $notFoundTriggered = true;
        return 'custom_404_handler';
    });

    $res404 = $router->dispatch('GET', '/une-route-inexistante');
    assertCondition($notFoundTriggered === true, "Une URL inconnue declenche le gestionnaire 404");
    assertCondition($res404 === 'custom_404_handler', "Le gestionnaire 404 retourne la reponse d'erreur appropriee");

    $indexFile = file_get_contents(__DIR__ . '/../public/index.php');
    assertCondition(str_contains($indexFile, "'/copies'"), "public/index.php enregistre la route /copies");
    assertCondition(str_contains($indexFile, "'/copies/create'"), "public/index.php enregistre la route /copies/create");
    assertCondition(str_contains($indexFile, "'/copies/{id}'"), "public/index.php enregistre la route /copies/{id}");
    assertCondition(str_contains($indexFile, 'setNotFoundHandler'), "public/index.php configure le gestionnaire d'erreur 404");

    $filesToCheck = [
        __DIR__ . '/../src/Router/Route.php',
        __DIR__ . '/../src/Router/Router.php',
        __DIR__ . '/../public/index.php',
    ];

    $hasComments = false;
    foreach ($filesToCheck as $filePath) {
        $lines = explode("\n", file_get_contents($filePath));
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                $hasComments = true;
                break 2;
            }
        }
    }
    assertCondition(!$hasComments, "Conformite de style : zero commentaire dans Route, Router et public/index.php");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
