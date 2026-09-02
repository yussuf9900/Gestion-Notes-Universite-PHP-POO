<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Dto\SoumettreCopieDTO;
use App\Entity\CopieExamen;
use App\Repository\CopieExamenRepositoryInterface;
use App\Repository\Database;
use App\Repository\PdoCopieExamenRepository;
use App\Service\CalculNoteAvecRetardService;
use App\Service\CalculNoteInterface;
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

echo "=== Suite de Tests — Partie 7 : Service Applicatif de Soumission ===\n\n";

try {
    $pdo = Database::getConnection();
    $repo = new PdoCopieExamenRepository($pdo);
    $calculService = new CalculNoteAvecRetardService();

    $service = new SoumissionCopieService($repo, $calculService);
    assertCondition($service->getRepository() === $repo, "Le service injecte correctement le repository");
    assertCondition($service->getCalculNoteService() === $calculService, "Le service injecte correctement le service de calcul");

    $dtoAlHeure = new SoumettreCopieDTO(
        18.0,
        new \DateTimeImmutable('2026-06-30 09:00:00'),
        new \DateTimeImmutable('2026-06-30 11:00:00')
    );
    $copieAlHeure = $service->soumettre($dtoAlHeure);
    assertCondition($copieAlHeure instanceof CopieExamen, "soumettre() retourne une instance de CopieExamen");
    assertCondition($copieAlHeure->getId() !== null && $copieAlHeure->getId() > 0, "La copie soumise possede un identifiant genere");
    assertCondition($copieAlHeure->getPenaliteAppliquee() === 0.0, "Copie a l'heure : 0.0 de penalite appliquee");
    assertCondition($copieAlHeure->getNoteFinale() === 18.0, "Copie a l'heure : note finale egale a la note brute");

    $dtoEnRetard = new SoumettreCopieDTO(
        14.0,
        new \DateTimeImmutable('2026-06-30 14:00:00'),
        new \DateTimeImmutable('2026-06-30 12:00:00')
    );
    $copieEnRetard = $service->soumettre($dtoEnRetard);
    assertCondition($copieEnRetard->getPenaliteAppliquee() === 2.0, "Copie en retard : 2.0 points de penalite deduits");
    assertCondition($copieEnRetard->getNoteFinale() === 12.0, "Copie en retard : note finale = 14.0 - 2.0 = 12.0");

    $dtoNoteBasse = new SoumettreCopieDTO(
        1.5,
        new \DateTimeImmutable('2026-06-30 14:00:00'),
        new \DateTimeImmutable('2026-06-30 12:00:00')
    );
    $copieNoteBasse = $service->soumettre($dtoNoteBasse);
    assertCondition($copieNoteBasse->getNoteFinale() === 0.0, "Copie avec note basse en retard : plancher a 0 garanti");

    $copieRetrouvee = $service->consulterCopie($copieAlHeure->getId());
    assertCondition($copieRetrouvee !== null && $copieRetrouvee->getId() === $copieAlHeure->getId(), "consulterCopie() retrouve la copie par son identifiant");

    $copiesListe = $service->listerCopies();
    assertCondition(is_array($copiesListe) && count($copiesListe) >= 2, "listerCopies() retourne la collection des copies");

    $reflector = new \ReflectionClass(SoumissionCopieService::class);
    $soumettreParam = $reflector->getMethod('soumettre')->getParameters()[0];
    assertCondition($soumettreParam->getType()?->getName() === SoumettreCopieDTO::class, "soumettre() type-hint strictement SoumettreCopieDTO");

    $hasComments = false;
    $lines = explode("\n", file_get_contents(__DIR__ . '/../src/Service/SoumissionCopieService.php'));
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
            $hasComments = true;
            break;
        }
    }
    assertCondition(!$hasComments, "Conformite de style : zero commentaire dans SoumissionCopieService");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
