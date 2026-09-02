<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CopieExamen;
use App\Rule\ReglePenaliteFixe;
use App\Rule\ReglePenaliteInterface;

class CalculNoteAvecRetardService implements CalculNoteInterface
{
    private ReglePenaliteInterface $reglePenalite;

    public function __construct(?ReglePenaliteInterface $reglePenalite = null)
    {
        $this->reglePenalite = $reglePenalite ?? new ReglePenaliteFixe(2.0);
    }

    public function calculerPenalite(CopieExamen $copie): float
    {
        return $this->reglePenalite->calculerPenalite($copie);
    }

    public function calculer(CopieExamen $copie): float
    {
        $penalite = $this->calculerPenalite($copie);
        $noteFinale = $copie->getNoteBrute() - $penalite;

        $copie->setPenaliteAppliquee($penalite);
        $copie->setNoteFinale($noteFinale);

        return $noteFinale;
    }

    public function getReglePenalite(): ReglePenaliteInterface
    {
        return $this->reglePenalite;
    }
}
