<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Dto\SoumettreCopieDTO;
use App\Service\DateConverterService;
use App\Validator\CopieValidator;

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

echo "=== Suite de Tests — Partie 4 : SoumettreCopieDTO, Validator & DateConverterService ===\n\n";

try {
    $dateDepot = new \DateTimeImmutable('2026-06-20 10:00:00');
    $dateLimite = new \DateTimeImmutable('2026-06-20 12:00:00');
    $dto1 = new SoumettreCopieDTO(15.5, $dateDepot, $dateLimite);

    assertCondition($dto1->getNoteBrute() === 15.5, "Instanciation directe avec float et DateTimeImmutable (note: 15.5)");
    assertCondition($dto1->getDateDepot() === $dateDepot, "Conservation de l'instance dateDepot");
    assertCondition($dto1->getDateLimite() === $dateLimite, "Conservation de l'instance dateLimite");

    $dto2 = SoumettreCopieDTO::fromArray([
        'noteBrute' => '18.25',
        'dateDepot' => '2026-06-21 14:00:00',
        'dateLimite' => '2026-06-21 16:00:00',
    ]);
    assertCondition($dto2->getNoteBrute() === 18.25, "Conversion automatique de note brute sous forme de chaine ('18.25' -> 18.25)");
    assertCondition($dto2->getDateDepot()->format('Y-m-d H:i:s') === '2026-06-21 14:00:00', "Conversion de dateDepot sous forme de chaine en DateTimeImmutable");
    assertCondition($dto2->getDateLimite()->format('Y-m-d H:i:s') === '2026-06-21 16:00:00', "Conversion de dateLimite sous forme de chaine en DateTimeImmutable");

    $postDataCamel = [
        'noteBrute' => '14.0',
        'dateDepot' => '2026-06-22 09:30:00',
        'dateLimite' => '2026-06-22 10:00:00',
    ];
    $dtoFromCamel = SoumettreCopieDTO::fromArray($postDataCamel);
    assertCondition($dtoFromCamel->getNoteBrute() === 14.0, "SoumettreCopieDTO::fromArray() avec cles camelCase (noteBrute)");
    assertCondition($dtoFromCamel->getDateDepot()->format('Y-m-d H:i:s') === '2026-06-22 09:30:00', "SoumettreCopieDTO::fromArray() conversion dateDepot");
    assertCondition($dtoFromCamel->getDateLimite()->format('Y-m-d H:i:s') === '2026-06-22 10:00:00', "SoumettreCopieDTO::fromArray() conversion dateLimite");

    $postDataSnake = [
        'note_brute' => '16.75',
        'date_depot' => '2026-06-23 08:00:00',
        'date_limite' => '2026-06-23 11:00:00',
    ];
    $dtoFromSnake = SoumettreCopieDTO::fromArray($postDataSnake);
    assertCondition($dtoFromSnake->getNoteBrute() === 16.75, "SoumettreCopieDTO::fromArray() avec cles snake_case (note_brute)");

    $arrayExport = $dtoFromCamel->toArray();
    assertCondition(
        isset($arrayExport['noteBrute'], $arrayExport['dateDepot'], $arrayExport['dateLimite'])
        && $arrayExport['noteBrute'] === 14.0
        && $arrayExport['dateDepot'] instanceof \DateTimeImmutable
        && $arrayExport['dateLimite'] instanceof \DateTimeImmutable,
        "SoumettreCopieDTO::toArray() exporte un tableau structure conforme"
    );

    $reflector = new \ReflectionClass(SoumettreCopieDTO::class);
    assertCondition($reflector->isReadOnly(), "SoumettreCopieDTO est declaree readonly class (PHP 8.2+)");

    $caughtMissingNote = false;
    try {
        SoumettreCopieDTO::fromArray([
            'dateDepot' => '2026-06-20 10:00:00',
            'dateLimite' => '2026-06-20 12:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtMissingNote = true;
    }
    assertCondition($caughtMissingNote, "Rejet avec InvalidArgumentException si noteBrute est absente");

    $caughtNonNumericNote = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => 'invalide_note',
            'dateDepot' => '2026-06-20 10:00:00',
            'dateLimite' => '2026-06-20 12:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtNonNumericNote = true;
    }
    assertCondition($caughtNonNumericNote, "Rejet avec InvalidArgumentException si noteBrute n'est pas numerique");

    $caughtNegativeNote = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => -2.5,
            'dateDepot' => '2026-06-20 10:00:00',
            'dateLimite' => '2026-06-20 12:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtNegativeNote = true;
    }
    assertCondition($caughtNegativeNote, "Rejet avec InvalidArgumentException si noteBrute < 0");

    $caughtOverflowNote = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => 20.5,
            'dateDepot' => '2026-06-20 10:00:00',
            'dateLimite' => '2026-06-20 12:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtOverflowNote = true;
    }
    assertCondition($caughtOverflowNote, "Rejet avec InvalidArgumentException si noteBrute > 20");

    $caughtMissingDateDepot = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => 15.0,
            'dateLimite' => '2026-06-20 12:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtMissingDateDepot = true;
    }
    assertCondition($caughtMissingDateDepot, "Rejet avec InvalidArgumentException si dateDepot est absente");

    $caughtInvalidDateDepot = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => 15.0,
            'dateDepot' => 'chaine-date-totalement-invalide',
            'dateLimite' => '2026-06-20 12:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtInvalidDateDepot = true;
    }
    assertCondition($caughtInvalidDateDepot, "Rejet avec InvalidArgumentException si format dateDepot invalide");

    $caughtMissingDateLimite = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => 15.0,
            'dateDepot' => '2026-06-20 10:00:00',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtMissingDateLimite = true;
    }
    assertCondition($caughtMissingDateLimite, "Rejet avec InvalidArgumentException si dateLimite est absente");

    $caughtInvalidDateLimite = false;
    try {
        SoumettreCopieDTO::fromArray([
            'noteBrute' => 15.0,
            'dateDepot' => '2026-06-20 10:00:00',
            'dateLimite' => 'format-date-incorrect',
        ]);
    } catch (\InvalidArgumentException $e) {
        $caughtInvalidDateLimite = true;
    }
    assertCondition($caughtInvalidDateLimite, "Rejet avec InvalidArgumentException si format dateLimite invalide");

    $validatorNote = CopieValidator::validerNoteBrute('17.5');
    assertCondition($validatorNote === 17.5, "CopieValidator::validerNoteBrute() convertit correctement une note valide");

    $dateConv = DateConverterService::convertir('2026-09-01 08:00:00');
    assertCondition($dateConv instanceof \DateTimeImmutable, "DateConverterService::convertir() retourne une instance de DateTimeImmutable");

    $forbiddenMethods = ['save', 'persist', 'insert', 'calculerNote', 'calculerPenalite', 'render', 'toHtml'];
    $hasForbiddenMethod = false;
    foreach ($forbiddenMethods as $method) {
        if ($reflector->hasMethod($method)) {
            $hasForbiddenMethod = true;
            break;
        }
    }
    assertCondition(!$hasForbiddenMethod, "SoumettreCopieDTO ne contient aucune methode de persistance, de calcul ou de rendu HTML");

    $filesToCheck = [
        __DIR__ . '/../src/Dto/SoumettreCopieDTO.php',
        __DIR__ . '/../src/Validator/CopieValidator.php',
        __DIR__ . '/../src/Service/DateConverterService.php',
    ];

    $hasCommentsTotal = false;
    foreach ($filesToCheck as $filePath) {
        $content = file_get_contents($filePath);
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                $hasCommentsTotal = true;
                break 2;
            }
        }
    }
    assertCondition(!$hasCommentsTotal, "Conformite de style : zero commentaire dans DTO, Validator et DateConverterService");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
