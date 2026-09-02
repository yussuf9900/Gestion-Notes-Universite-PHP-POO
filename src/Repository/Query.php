<?php

declare(strict_types=1);

namespace App\Repository;

 abstract class Query
{
    protected \PDO $pdo;

    protected function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function prepare(string $sql, array $options = []): \PDOStatement
    {
        return $this->pdo->prepare($sql, $options);
    }

    protected function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql);
    }

    private function executeQuery(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->executeQuery($sql, $params)->fetchAll();
    }

    protected function fetch(string $sql, array $params = []): array|false
    {
        return $this->executeQuery($sql, $params)->fetch();
    }

    protected function lastInsertId(?string $name = null): string|false
    {
        return $this->pdo->lastInsertId($name);
    }

    protected function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    protected function commit(): bool
    {
        return $this->pdo->commit();
    }

    protected function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    protected function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }
}
