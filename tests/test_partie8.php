<?php

declare(strict_types=1);

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

echo "=== Suite de Tests — Partie 8 : Vues du Système de Notation ===\n\n";

try {
    $templates = [
        'header' => __DIR__ . '/../templates/layout/header.php',
        'footer' => __DIR__ . '/../templates/layout/footer.php',
        'create' => __DIR__ . '/../templates/copies/create.php',
        'index' => __DIR__ . '/../templates/copies/index.php',
        'show' => __DIR__ . '/../templates/copies/show.php',
        '404' => __DIR__ . '/../templates/errors/404.php',
        'error' => __DIR__ . '/../templates/errors/error.php',
    ];

    foreach ($templates as $name => $path) {
        assertCondition(file_exists($path), "Le template '{$name}' existe bien ({$path})");
        $output = [];
        $exitCode = 0;
        exec("php -l " . escapeshellarg($path), $output, $exitCode);
        assertCondition($exitCode === 0, "Syntaxe PHP valide pour le template '{$name}'");
    }

    $createContent = file_get_contents($templates['create']);
    assertCondition(
        str_contains($createContent, 'method="POST"') || str_contains($createContent, 'method=\'POST\''),
        "Le formulaire de soumission utilise explicitement la methode HTTP POST"
    );
    assertCondition(
        str_contains($createContent, 'action="/copies"') || str_contains($createContent, 'action=\'/copies\''),
        "Le formulaire cible la route POST /copies"
    );
    assertCondition(str_contains($createContent, 'name="noteBrute"'), "Le formulaire contient le champ noteBrute");
    assertCondition(str_contains($createContent, 'name="dateDepot"'), "Le formulaire contient le champ dateDepot");
    assertCondition(str_contains($createContent, 'name="dateLimite"'), "Le formulaire contient le champ dateLimite");

    $forbiddenSql = ['SELECT ', 'INSERT INTO', 'UPDATE ', 'DELETE FROM', 'PDO', 'prepare(', 'execute('];
    $hasSql = false;
    $sqlLocation = '';
    foreach ($templates as $name => $path) {
        $content = file_get_contents($path);
        foreach ($forbiddenSql as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $hasSql = true;
                $sqlLocation = "{$keyword} dans {$name}";
                break 2;
            }
        }
    }
    assertCondition(!$hasSql, "Aucune requete SQL n'apparait dans les vues (test : {$sqlLocation})");

    $forbiddenCalc = ['calculerPenalite', 'calculerNote', '$noteBrute - 2', '$copie->getNoteBrute() - 2'];
    $hasCalc = false;
    foreach ($templates as $name => $path) {
        $content = file_get_contents($path);
        foreach ($forbiddenCalc as $term) {
            if (str_contains($content, $term)) {
                $hasCalc = true;
                break 2;
            }
        }
    }
    assertCondition(!$hasCalc, "Aucune penalite n'est calculee directement dans les vues");

    $indexContent = file_get_contents($templates['index']);
    $showContent = file_get_contents($templates['show']);
    assertCondition(str_contains($indexContent, 'htmlspecialchars'), "Les valeurs de la liste sont echappees via htmlspecialchars");
    assertCondition(str_contains($showContent, 'htmlspecialchars'), "Les valeurs du detail sont echappees via htmlspecialchars");

    $hasComments = false;
    foreach ($templates as $name => $path) {
        $lines = explode("\n", file_get_contents($path));
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#') || str_starts_with($trimmed, '/*') || str_starts_with($trimmed, '*')) {
                $hasComments = true;
                break 2;
            }
        }
    }
    assertCondition(!$hasComments, "Conformite de style : zero commentaire dans l'ensemble des templates HTML/PHP");

} catch (\Throwable $e) {
    echo "\033[31mErreur inattendue : " . $e->getMessage() . "\033[0m\n";
    $failed++;
}

echo "\nResultat final : {$passed} reussis, {$failed} echoues.\n";
exit($failed === 0 ? 0 : 1);
