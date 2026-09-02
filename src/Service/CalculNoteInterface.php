<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CopieExamen;

interface CalculNoteInterface
{
    public function calculer(CopieExamen $copie): float;
    public function calculerPenalite(CopieExamen $copie): float;
}
