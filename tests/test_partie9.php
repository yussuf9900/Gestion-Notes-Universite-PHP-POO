<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\BaseController;
use App\Controller\CopieExamenController;
use App\Dto\SoumettreCopieDTO;
use App\Entity\CopieExamen;
use App\Repository\Database;
use App\Repository\PdoCopieExamenRepository;
use App\Service\CalculNoteAvecRetardService;
use App\Service\SoumissionCopieService;

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

echo "=== Suite de Tests — Partie 9 : Contrôleur MVC des Copies ===\n\n";

try {
    $reflectorCtrl = new \ReflectionClass(CopieExamenController::class);
    assertCondition($reflectorCtrl->isSubclassOf(BaseController::class), "CopieExamenController herite de BaseController");

    $constructor = $reflectorCtrl->getConstructor();
    assertCondition($constructor !== null, "Le controleur possede un constructeur");
    $firstParam = $constructor->getParameters()[0] ?? null;
    assertCondition($firstParam?->getType()?->getName() === SoumissionCopieService::class, "Le controleur recoit SoumissionCopieService par injection");

    $forbiddenSql = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'prepare', 'query', 'fetchAll'];
    $hasSqlInController = false;
    $ctrlFileContent = file_get_contents(__DIR__ . '/../src/Controller/CopieExamenController.php');
    foreach ($forbiddenSql as $sqlWord) {
        if (preg_match('/\b' . $sqlWord . '\b/i', $ctrlFileContent)) {
            $hasSqlInController = true;
            break;
        }
    }
    assertCondition(!$hasSqlInController, "Le controleur ne contient aucune requete SQL");

    $forbiddenCalc = ['calculerPenalite', 'calculerNote', '- 2.0', '- 2'];
    $hasCalcInController = false;
    foreach ($forbiddenCalc as $calcWord) {
        if (str_contains($ctrlFileContent, $calcWord)) {
            $hasCalcInController = true;
            break;
        }
    }
    assertCondition(!$hasCalcInController, "Le controleur ne calcule pas la penalite");

    assertCondition($reflectorCtrl->hasMethod('index'), "Le controleur definit l'action index()");
    assertCondition($reflectorCtrl->hasMethod('create'), "Le controleur definit l'action create()");
    assertCondition($reflectorCtrl->hasMethod('store'), "Le controleur definit l'action store()");
    assertCondition($reflectorCtrl->hasMethod('show'), "Le controleur definit l'action show()");

    $pdo = Database::getConnection();
    $repository = new PdoCopieExamenRepository($pdo);
    $service = new SoumissionCopieService($repository, new CalculNoteAvecRetardService());
    $controller = new CopieExamenController($service);

    ob_start();
    $controller->index();
    $indexHtml = ob_get_clean();
    assertCondition(str_contains($indexHtml, 'Liste des copies d\'examen'), "index() produit le rendu HTML de la liste des copies");

    ob_start();
    $controller->create();
    $createHtml = ob_get_clean();
    assertCondition(str_contains($createHtml, 'Soumettre une nouvelle copie'), "create() affiche le formulaire de soumission");

    ob_start();
    $controller->store([
        'noteBrute' => '17.0',
        'dateDepot' => '2026-07-01 10:00:00',
        'dateLimite' => '2026-07-01 12:00:00',
    ]);
    $storeSuccessHtml = ob_get_clean();
    assertCondition($controller->getStatusCode() === 302, "store() valide traite la soumission et declenche une redirection HTTP 302");

    ob_start();
    $controller->store([
        'noteBrute' => '25.0',
        'dateDepot' => '2026-07-01 10:00:00',
        'dateLimite' => '2026-07-01 12:00:00',
    ]);
    $storeErrorHtml = ob_get_clean();
    assertCondition($controller->getStatusCode() === 400, "store() avec note invalide declenche une reponse HTTP 400 (Bad Request)");
    assertCondition(str_contains($storeErrorHtml, 'Des erreurs ont été détectées'), "store() re-affiche le formulaire avec les messages d'erreur");

    $savedCopie = $service->soumettre(new SoumettreCopieDTO(
        16.0,
        new \DateTimeImmutable('2026-07-01 09:00:00'),
        new \DateTimeImmutable('2026-07-01 11:00:00')
    ));
    ob_start();
    $controller->show($savedCopie->getId());
    $showHtml = ob_get_clean();
    assertCondition(str_contains($showHtml, 'Détail de la copie #' . $savedCopie->getId()), "show() affiche le detail complet de la copie existante");

    ob_start();
    $controller->show(9999999);
    $show404Html = ob_get_clean();
    assertCondition($controller->getStatusCode() === 404, "show() avec identifiant inexistant retourne le code HTTP 404");
    assertCondition(str_contains($show404Html, 'Page non trouvée'), "show() avec identifiant inexistant affiche la page d'erreur 404");

    $filesToCheck = [
        __DIR__ . '/../src/Controller/BaseController.php',
        __DIR__ . '/../src/Controller/CopieExamenController.php',
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
    assertCondition(!$hasComments, "Conformite de style : zero commentaire dans BaseController et CopieExamenController");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
