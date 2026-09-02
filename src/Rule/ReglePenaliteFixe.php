<?php

declare(strict_types=1);

namespace App\Rule;

use App\Entity\CopieExamen;

class ReglePenaliteFixe implements ReglePenaliteInterface
{
    public function __construct(
        private float $montantPenalite = 2.0
    ) {
        if ($this->montantPenalite < 0.0) {
            throw new \InvalidArgumentException('Le montant de la penalite ne peut pas etre negatif.');
        }
    }

    public function calculerPenalite(CopieExamen $copie): float
    {
        if (!$copie->isEnRetard()) {
            return 0.0;
        }

        return $this->montantPenalite;
    }

    public function getMontantPenalite(): float
    {
        return $this->montantPenalite;
    }
}
