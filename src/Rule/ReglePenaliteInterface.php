<?php

declare(strict_types=1);

namespace App\Rule;

use App\Entity\CopieExamen;

interface ReglePenaliteInterface
{
    public function calculerPenalite(CopieExamen $copie): float;
}
