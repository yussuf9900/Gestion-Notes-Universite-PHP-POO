<?php

declare(strict_types=1);

namespace App\Dto;

readonly class SoumettreCopieDTO
{
    public float $noteBrute;
    public \DateTimeImmutable $dateDepot;
    public \DateTimeImmutable $dateLimite;

    public function __construct(
        float $noteBrute,
        \DateTimeImmutable $dateDepot,
        \DateTimeImmutable $dateLimite
    ) {
        $this->noteBrute = $noteBrute;
        $this->dateDepot = $dateDepot;
        $this->dateLimite = $dateLimite;
    }

    public function getNoteBrute(): float
    {
        return $this->noteBrute;
    }

    public function getDateDepot(): \DateTimeImmutable
    {
        return $this->dateDepot;
    }

    public function getDateLimite(): \DateTimeImmutable
    {
        return $this->dateLimite;
    }

    public function toArray(): array
    {
        return [
            'noteBrute' => $this->noteBrute,
            'dateDepot' => $this->dateDepot,
            'dateLimite' => $this->dateLimite,
        ];
    }
}
