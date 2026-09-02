<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Repository\Database;
use App\Repository\Query;

$config = require __DIR__ . '/../config/database.php';

try {
    $adminDsn = sprintf('%s:host=%s;port=%d;dbname=postgres', $config['driver'], $config['host'], $config['port']);
    $adminPdo = new PDO($adminDsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $stmt = $adminPdo->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
    $stmt->execute(['dbname' => $config['dbname']]);

    if (!$stmt->fetch()) {
        $adminPdo->exec(sprintf('CREATE DATABASE "%s"', $config['dbname']));
        echo "Base de donnees '{$config['dbname']}' creee avec succes.\n";
    } else {
        echo "Base de donnees '{$config['dbname']}' deja existante.\n";
    }

    $pdo = Database::getConnection();
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
    echo "Schema 'schema.sql' execute avec succes.\n";

    $queryManager = new Query($pdo);
    $rows = $queryManager->fetchAll("SELECT id, etudiant_nom, note_brute, penalite_appliquee, note_finale FROM copies ORDER BY id ASC");

    echo "Copies enregistrees :\n";
    foreach ($rows as $row) {
        echo sprintf(
            "- ID %d | %s | Note: %.2f | Penalite: %.2f | Finale: %.2f\n",
            $row['id'],
            $row['etudiant_nom'] ?? 'Inconnu',
            $row['note_brute'],
            $row['penalite_appliquee'],
            $row['note_finale']
        );
    }
} catch (\Throwable $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    exit(1);
}
