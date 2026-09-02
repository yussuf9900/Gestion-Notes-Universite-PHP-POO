<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CopieExamen;

interface CopieExamenRepositoryInterface
{
    public function save(CopieExamen $copie): CopieExamen;

    public function findAll(): array;

    public function findById(int $id): ?CopieExamen;
}
