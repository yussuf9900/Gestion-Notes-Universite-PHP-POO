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
        \DateTimeInterface|string $dateDepot,
        \DateTimeInterface|string $dateLimite
    ) {
        $this->noteBrute = $noteBrute;
        $this->dateDepot = $this->convertirDate($dateDepot, 'date de depot');
        $this->dateLimite = $this->convertirDate($dateLimite, 'date limite');
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

    private function convertirDate(\DateTimeInterface|string $date, string $nomChamp): \DateTimeImmutable
    {
        if ($date instanceof \DateTimeImmutable) {
            return $date;
        }

        if ($date instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($date);
        }

        if (is_string($date)) {
            try {
                return new \DateTimeImmutable($date);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException(
                    sprintf("Format de date invalide pour '%s' : %s", $nomChamp, $e->getMessage())
                );
            }
        }

        throw new \InvalidArgumentException(
            sprintf("Le champ '%s' doit etre une chaine de caracteres ou une instance de DateTimeInterface.", $nomChamp)
        );
    }
}
