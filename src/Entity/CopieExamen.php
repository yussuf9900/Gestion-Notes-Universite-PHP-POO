<?php

declare(strict_types=1);

namespace App\Entity;

class CopieExamen extends AbstractDocument
{
    private float $noteBrute;
    private float $noteFinale;
    private float $penaliteAppliquee = 0.0;
    private \DateTimeImmutable $dateLimite;

    public function __construct(
        \DateTimeInterface|string $dateLimite,
        float $noteBrute,
        float $noteFinale,
        float $penaliteAppliquee = 0.0,
        \DateTimeInterface|string $dateDepot = new \DateTimeImmutable(),
        ?int $id = null
    ) {
        parent::__construct($dateDepot, $id);
        $this->setDateLimite($dateLimite);
        $this->setNoteBrute($noteBrute);
        $this->setNoteFinale($noteFinale);
        $this->setPenaliteAppliquee($penaliteAppliquee);
    }

    public function getNoteBrute(): float
    {
        return $this->noteBrute;
    }

    public function setNoteBrute(float $noteBrute): static
    {
        $this->validerNote($noteBrute, 'note brute');
        $this->noteBrute = $noteBrute;
        return $this;
    }

    public function getNoteFinale(): float
    {
        return $this->noteFinale;
    }

    public function setNoteFinale(float $noteFinale): static
    {
        $this->validerNote($noteFinale, 'note finale');
        $this->noteFinale = $noteFinale;
        return $this;
    }

    public function getPenaliteAppliquee(): float
    {
        return $this->penaliteAppliquee;
    }

    public function setPenaliteAppliquee(float $penaliteAppliquee): static
    {
        if ($penaliteAppliquee < 0.0) {
            throw new \InvalidArgumentException(
                sprintf("La penalite appliquee ne peut pas etre negative (valeur recue : %.2f).", $penaliteAppliquee)
            );
        }
        $this->penaliteAppliquee = $penaliteAppliquee;
        return $this;
    }

    public function getDateLimite(): \DateTimeImmutable
    {
        return $this->dateLimite;
    }

    public function setDateLimite(\DateTimeInterface|string $dateLimite): static
    {
        if (is_string($dateLimite)) {
            try {
                $dateLimite = new \DateTimeImmutable($dateLimite);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Format de date limite invalide : " . $e->getMessage());
            }
        } elseif ($dateLimite instanceof \DateTime) {
            $dateLimite = \DateTimeImmutable::createFromMutable($dateLimite);
        }

        $this->dateLimite = $dateLimite;
        return $this;
    }

    public function isEnRetard(): bool
    {
        return $this->dateDepot > $this->dateLimite;
    }

    public function calculerRetardJours(): int
    {
        if (!$this->isEnRetard()) {
            return 0;
        }
        $diff = $this->dateLimite->diff($this->dateDepot);
        return (int) $diff->days;
    }

    private function validerNote(float $note, string $nomChamp): void
    {
        if ($note < 0.0 || $note > 20.0) {
            throw new \InvalidArgumentException(
                sprintf("La %s doit etre comprise entre 0 et 20 (valeur recue : %.2f).", $nomChamp, $note)
            );
        }
    }
}
