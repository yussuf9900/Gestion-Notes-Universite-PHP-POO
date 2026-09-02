<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\CopieExamen;
use App\Rule\ReglePenaliteFixe;
use App\Rule\ReglePenaliteInterface;
use App\Service\CalculNoteAvecRetardService;
use App\Service\CalculNoteInterface;

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

echo "=== Suite de Tests — Partie 5 : Stratégie de Calcul des Notes ===\n\n";

try {
    $service = new CalculNoteAvecRetardService();

    assertCondition($service instanceof CalculNoteInterface, "CalculNoteAvecRetardService implemente CalculNoteInterface (contrat)");
    assertCondition($service->getReglePenalite() instanceof ReglePenaliteInterface, "La regle de penalite implemente ReglePenaliteInterface");
    assertCondition($service->getReglePenalite() instanceof ReglePenaliteFixe, "Regle de penalite par defaut : ReglePenaliteFixe");

    $dateLimite = new \DateTimeImmutable('2026-06-20 12:00:00');
    $dateDepotATemps = new \DateTimeImmutable('2026-06-20 10:00:00');
    $copieATemps = new CopieExamen($dateLimite, 16.5, 16.5, 0.0, $dateDepotATemps);

    $penaliteATemps = $service->calculerPenalite($copieATemps);
    assertCondition($penaliteATemps === 0.0, "Depot a temps : la penalite calculee est de 0.0 point");

    $noteFinaleATemps = $service->calculer($copieATemps);
    assertCondition($noteFinaleATemps === 16.5, "Depot a temps : la note finale reste egale a la note brute (16.5)");
    assertCondition($copieATemps->getPenaliteAppliquee() === 0.0, "Depot a temps : penalite appliquee sur l'entite = 0.0");
    assertCondition($copieATemps->getNoteFinale() === 16.5, "Depot a temps : note finale sur l'entite = 16.5");

    $dateDepotPileALHeure = new \DateTimeImmutable('2026-06-20 12:00:00');
    $copiePileALHeure = new CopieExamen($dateLimite, 18.0, 18.0, 0.0, $dateDepotPileALHeure);
    $notePileALHeure = $service->calculer($copiePileALHeure);
    assertCondition($notePileALHeure === 18.0 && $copiePileALHeure->getPenaliteAppliquee() === 0.0, "Depot pile a la date limite : aucune penalite appliquee");

    $dateDepotEnRetard = new \DateTimeImmutable('2026-06-20 14:30:00');
    $copieEnRetard = new CopieExamen($dateLimite, 15.0, 15.0, 0.0, $dateDepotEnRetard);

    $penaliteEnRetard = $service->calculerPenalite($copieEnRetard);
    assertCondition($penaliteEnRetard === 2.0, "Depot en retard : la penalite calculee est exactement de 2.0 points");

    $noteFinaleEnRetard = $service->calculer($copieEnRetard);
    assertCondition($noteFinaleEnRetard === 13.0, "Depot en retard : la note finale perd deux points (15.0 - 2.0 = 13.0)");
    assertCondition($copieEnRetard->getPenaliteAppliquee() === 2.0, "Depot en retard : penalite appliquee sur l'entite = 2.0");
    assertCondition($copieEnRetard->getNoteFinale() === 13.0, "Depot en retard : note finale sur l'entite = 13.0");

    $copieNoteFaible1 = new CopieExamen($dateLimite, 1.5, 1.5, 0.0, $dateDepotEnRetard);
    $noteFaible1 = $service->calculer($copieNoteFaible1);
    assertCondition($noteFaible1 === 0.0, "Plancher a zero : note brute 1.5 en retard perd 2 points -> note finale = 0.0 (non negative)");
    assertCondition($copieNoteFaible1->getNoteFinale() === 0.0, "Plancher a zero : note finale sur l'entite = 0.0");
    assertCondition($copieNoteFaible1->getPenaliteAppliquee() === 2.0, "Plancher a zero : penalite appliquee sur l'entite reste 2.0");

    $copieNoteZero = new CopieExamen($dateLimite, 0.0, 0.0, 0.0, $dateDepotEnRetard);
    $noteZero = $service->calculer($copieNoteZero);
    assertCondition($noteZero === 0.0, "Plancher a zero : note brute 0.0 en retard perd 2 points -> note finale = 0.0");

    $copieNoteUn = new CopieExamen($dateLimite, 1.0, 1.0, 0.0, $dateDepotEnRetard);
    $noteUn = $service->calculer($copieNoteUn);
    assertCondition($noteUn === 0.0, "Plancher a zero : note brute 1.0 en retard perd 2 points -> note finale = 0.0 (non -1.0)");

    function executerCalcul(CalculNoteInterface $calculateur, CopieExamen $copie): float
    {
        return $calculateur->calculer($copie);
    }

    $resultatViaInterface = executerCalcul($service, $copieEnRetard);
    assertCondition($resultatViaInterface === 13.0, "Polymorphisme : le calcul depend strictement du contrat CalculNoteInterface");

    $strategieSansPenalite = new class implements CalculNoteInterface {
        public function calculer(CopieExamen $copie): float
        {
            $copie->setPenaliteAppliquee(0.0);
            $copie->setNoteFinale($copie->getNoteBrute());
            return $copie->getNoteBrute();
        }
        public function calculerPenalite(CopieExamen $copie): float
        {
            return 0.0;
        }
    };

    $resultatSansPenalite = executerCalcul($strategieSansPenalite, $copieEnRetard);
    assertCondition($resultatSansPenalite === 15.0, "Strategy Pattern : interchangeabilite dynamique avec une autre strategie de calcul");

    $reglePenalitePersonnalisee = new ReglePenaliteFixe(5.0);
    $servicePersonnalise = new CalculNoteAvecRetardService($reglePenalitePersonnalisee);
    $copieTest5pts = new CopieExamen($dateLimite, 17.0, 17.0, 0.0, $dateDepotEnRetard);
    $noteTest5pts = $servicePersonnalise->calculer($copieTest5pts);
    assertCondition($noteTest5pts === 12.0 && $copieTest5pts->getPenaliteAppliquee() === 5.0, "Extensibilite : injection d'une penalite personnalisee (5.0 points)");

    $filesToCheck = [
        __DIR__ . '/../src/Service/CalculNoteInterface.php',
        __DIR__ . '/../src/Service/CalculNoteAvecRetardService.php',
        __DIR__ . '/../src/Rule/ReglePenaliteInterface.php',
        __DIR__ . '/../src/Rule/ReglePenaliteFixe.php',
    ];

    $hasComments = false;
    foreach ($filesToCheck as $filePath) {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                $hasComments = true;
                break 2;
            }
        }
    }
    assertCondition(!$hasComments, "Conformite de style : zero commentaire dans l'ensemble des fichiers de la Partie 5");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
