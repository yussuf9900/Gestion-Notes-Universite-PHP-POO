<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Repository\Database;
use App\Repository\Query;

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

echo "=== Suite de Tests — Partie 3 : Repository, Database & Query ===\n\n";

try {
    $db1 = Database::getConnection();
    $db2 = Database::getConnection();
    assertCondition($db1 instanceof PDO, "Connexion PDO instanciee via Database::getConnection()");
    assertCondition($db1 === $db2, "Pattern Singleton : instance unique de connexion PDO");

    $query = new Query();
    assertCondition($query->getPdo() === $db1, "Query utilise l'instance Singleton PDO par defaut");

    $stmt = $query->executeQuery("
        INSERT INTO copies (date_depot, date_limite, note_brute, penalite_appliquee, note_finale, etudiant_nom, matricule, matiere)
        VALUES (:date_depot, :date_limite, :note_brute, :penalite_appliquee, :note_finale, :etudiant_nom, :matricule, :matiere)
        RETURNING id
    ", [
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
    assertCondition($insertedId > 0, "Query::executeQuery() insertion reussie (ID: {$insertedId})");

    $row = $query->fetch("SELECT * FROM copies WHERE id = :id", ['id' => $insertedId]);
    assertCondition($row !== false && (float) $row['note_finale'] === 18.00, "Query::fetch() lecture conforme de l'enregistrement");

    $allRows = $query->fetchAll("SELECT * FROM copies WHERE matricule = :matricule", ['matricule' => 'ETU003']);
    assertCondition(is_array($allRows) && count($allRows) >= 1, "Query::fetchAll() retourne les enregistrements sous forme de tableau");

    $queryDirectStmt = $query->query("SELECT COUNT(*) AS total FROM copies");
    $totalCount = (int) $queryDirectStmt->fetchColumn();
    assertCondition($totalCount > 0, "Query::query() execution directe reussie (Total copies: {$totalCount})");

    $prepStmt = $query->prepare("SELECT id, etudiant_nom FROM copies WHERE id = :id");
    assertCondition($prepStmt instanceof PDOStatement, "Query::prepare() retourne une instance valide de PDOStatement");

    $checkViolation = false;
    try {
        $query->executeQuery("
            INSERT INTO copies (date_depot, date_limite, note_brute, penalite_appliquee, note_finale)
            VALUES ('2026-06-20 10:00:00', '2026-06-20 12:00:00', 25.00, 0.00, 25.00)
        ");
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
