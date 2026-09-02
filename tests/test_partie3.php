<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;

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

echo "=== Suite de Tests — Partie 3 : Preparer la Persistance ===\n\n";

try {
    $db1 = Database::getConnection();
    $db2 = Database::getConnection();
    assertCondition($db1 instanceof PDO, "Connexion PDO instanciee");
    assertCondition($db1 === $db2, "Pattern Singleton : instance unique de connexion");

    $stmt = $db1->prepare("
        INSERT INTO copies (date_depot, date_limite, note_brute, penalite_appliquee, note_finale, etudiant_nom, matricule, matiere)
        VALUES (:date_depot, :date_limite, :note_brute, :penalite_appliquee, :note_finale, :etudiant_nom, :matricule, :matiere)
        RETURNING id
    ");

    $stmt->execute([
        'date_depot' => '2026-06-20 11:30:00',
        'date_limite' => '2026-06-20 12:00:00',
        'note_brute' => 18.00,
        'penalite_appliquee' => 0.00,
        'note_finale' => 18.00,
        'etudiant_nom' => 'Claire Bernard',
        'matricule' => 'ETU003',
        'matiere' => 'Bases de Donnees',
    ]);

    $insertedId = (int) $stmt->fetchColumn();
    assertCondition($insertedId > 0, "Insertion reussie via requete preparee (ID: {$insertedId})");

    $selectStmt = $db1->prepare("SELECT * FROM copies WHERE id = :id");
    $selectStmt->execute(['id' => $insertedId]);
    $row = $selectStmt->fetch();

    assertCondition($row !== false && (float) $row['note_finale'] === 18.00, "Lecture conforme de l'enregistrement cree");

    $checkViolation = false;
    try {
        $invalidStmt = $db1->prepare("
            INSERT INTO copies (date_depot, date_limite, note_brute, penalite_appliquee, note_finale)
            VALUES ('2026-06-20 10:00:00', '2026-06-20 12:00:00', 25.00, 0.00, 25.00)
        ");
        $invalidStmt->execute();
    } catch (\PDOException $e) {
        $checkViolation = true;
    }
    assertCondition($checkViolation, "Rejet strict d'une note > 20 par la contrainte CHECK SQL");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
