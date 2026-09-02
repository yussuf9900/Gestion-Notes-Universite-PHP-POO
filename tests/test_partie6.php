<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\CopieExamen;
use App\Repository\CopieExamenRepositoryInterface;
use App\Repository\Database;
use App\Repository\PdoCopieExamenRepository;

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

echo "=== Suite de Tests — Partie 6 : Repository des Copies ===\n\n";

try {
    $pdo = Database::getConnection();

    $reflectorInterface = new \ReflectionClass(CopieExamenRepositoryInterface::class);
    assertCondition($reflectorInterface->isInterface(), "CopieExamenRepositoryInterface est bien une interface PHP");
    assertCondition($reflectorInterface->hasMethod('save'), "L'interface definit save()");
    assertCondition($reflectorInterface->hasMethod('findAll'), "L'interface definit findAll()");
    assertCondition($reflectorInterface->hasMethod('findById'), "L'interface definit findById()");

    $repository = new PdoCopieExamenRepository($pdo);
    assertCondition($repository instanceof CopieExamenRepositoryInterface, "PdoCopieExamenRepository respecte son interface");

    $reflectorClass = new \ReflectionClass(PdoCopieExamenRepository::class);
    $constructor = $reflectorClass->getConstructor();
    assertCondition($constructor !== null, "PdoCopieExamenRepository possede un constructeur");
    $params = $constructor->getParameters();
    assertCondition(count($params) >= 1 && $params[0]->getType()?->getName() === PDO::class, "Le Repository recoit PDO par son constructeur");

    $forbiddenMethods = ['calculer', 'calculerNote', 'calculerPenalite', 'isEnRetard'];
    $hasCalculation = false;
    foreach ($forbiddenMethods as $meth) {
        if ($reflectorClass->hasMethod($meth)) {
            $hasCalculation = true;
            break;
        }
    }
    assertCondition(!$hasCalculation, "Le Repository ne calcule pas la note ni les penalites");

    $dateDepot = new \DateTimeImmutable('2026-06-25 09:00:00');
    $dateLimite = new \DateTimeImmutable('2026-06-25 10:00:00');
    $nouvelleCopie = new CopieExamen(
        dateLimite: $dateLimite,
        noteBrute: 15.50,
        noteFinale: 15.50,
        penaliteAppliquee: 0.00,
        dateDepot: $dateDepot
    );

    assertCondition($nouvelleCopie->getId() === null, "Nouvelle copie creee sans identifiant prealable");
    $savedCopie = $repository->save($nouvelleCopie);
    assertCondition($savedCopie->getId() !== null && $savedCopie->getId() > 0, "save() persiste la copie et injecte un identifiant valide");

    $retrieved = $repository->findById($savedCopie->getId());
    assertCondition($retrieved instanceof CopieExamen, "findById() retourne une instance de CopieExamen");
    assertCondition($retrieved->getId() === $savedCopie->getId(), "findById() retrouve la copie avec le bon ID");
    assertCondition($retrieved->getNoteBrute() === 15.50, "findById() restitue la bonne note brute");
    assertCondition($retrieved->getNoteFinale() === 15.50, "findById() restitue la bonne note finale");
    assertCondition($retrieved->getPenaliteAppliquee() === 0.00, "findById() restitue la bonne penalite");
    assertCondition($retrieved->getDateDepot()->format('Y-m-d H:i:s') === '2026-06-25 09:00:00', "findById() restitue la bonne date de depot");
    assertCondition($retrieved->getDateLimite()->format('Y-m-d H:i:s') === '2026-06-25 10:00:00', "findById() restitue la bonne date limite");

    $notFound = $repository->findById(9999999);
    assertCondition($notFound === null, "findById() retourne null si aucun enregistrement ne correspond");

    $savedCopie->setNoteBrute(17.00);
    $savedCopie->setNoteFinale(17.00);
    $updatedCopie = $repository->save($savedCopie);
    $reRead = $repository->findById($savedCopie->getId());
    assertCondition($reRead->getNoteBrute() === 17.00, "save() met a jour un enregistrement existant (UPDATE)");

    $allCopies = $repository->findAll();
    assertCondition(is_array($allCopies), "findAll() retourne un tableau");
    assertCondition(count($allCopies) >= 1, "findAll() retourne au moins une copie");
    $allInstances = true;
    foreach ($allCopies as $item) {
        if (!$item instanceof CopieExamen) {
            $allInstances = false;
            break;
        }
    }
    assertCondition($allInstances, "Tous les elements retournes par findAll() sont des instances de CopieExamen");

    $filesToCheck = [
        __DIR__ . '/../src/Repository/CopieExamenRepositoryInterface.php',
        __DIR__ . '/../src/Repository/PdoCopieExamenRepository.php',
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
    assertCondition(!$hasComments, "Conformite de style : zero commentaire dans l'ensemble des fichiers de la Partie 6");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
