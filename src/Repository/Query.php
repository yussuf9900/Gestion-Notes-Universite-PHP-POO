<?php

declare(strict_types=1);

namespace App\Repository;

class Query
{
    private \PDO $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function prepare(string $sql, array $options = []): \PDOStatement
    {
        return $this->pdo->prepare($sql, $options);
    }

    public function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql);
    }

    public function executeQuery(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->executeQuery($sql, $params)->fetchAll();
    }

    public function fetch(string $sql, array $params = []): array|false
    {
        return $this->executeQuery($sql, $params)->fetch();
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return $this->pdo->lastInsertId($name);
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}
