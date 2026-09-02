<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    public static function getConnection(?array $customConfig = null): PDO
    {
        if (self::$instance === null) {
            $config = $customConfig ?? require __DIR__ . '/../../config/database.php';

            $dsn = sprintf(
                '%s:host=%s;port=%d;dbname=%s',
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['dbname']
            );

            try {
                self::$instance = new PDO(
                    $dsn,
                    $config['user'],
                    $config['password'],
                    $config['options'] ?? []
                );
            } catch (PDOException $e) {
                throw new PDOException("Erreur de connexion a la base de donnees : " . $e->getMessage(), (int) $e->getCode(), $e);
            }
        }

        return self::$instance;
    }

    public static function disconnect(): void
    {
        self::$instance = null;
    }
}
