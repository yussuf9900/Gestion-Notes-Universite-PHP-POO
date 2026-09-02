<?php

declare(strict_types=1);

namespace App\Dto;

readonly class SoumettreCopieDTO
{
    public float $noteBrute;
    public \DateTimeImmutable $dateDepot;
    public \DateTimeImmutable $dateLimite;

    public function __construct(
        float|string|null $noteBrute,
        \DateTimeInterface|string|null $dateDepot,
        \DateTimeInterface|string|null $dateLimite
    ) {
        if ($noteBrute === null || $noteBrute === '' || !is_numeric($noteBrute)) {
            throw new \InvalidArgumentException('La note brute doit etre une valeur numerique valide.');
        }

        $noteFloat = (float) $noteBrute;
        if ($noteFloat < 0.0 || $noteFloat > 20.0) {
            throw new \InvalidArgumentException(
                sprintf('La note brute doit etre comprise entre 0 et 20 (valeur recue : %.2f).', $noteFloat)
            );
        }
        $this->noteBrute = $noteFloat;

        $this->dateDepot = $this->convertirDate($dateDepot, 'date de depot');
        $this->dateLimite = $this->convertirDate($dateLimite, 'date limite');
    }

    public static function fromArray(array $data): self
    {
        $noteBrute = $data['noteBrute'] ?? $data['note_brute'] ?? $data['note'] ?? null;
        $dateDepot = $data['dateDepot'] ?? $data['date_depot'] ?? null;
        $dateLimite = $data['dateLimite'] ?? $data['date_limite'] ?? null;

        return new self($noteBrute, $dateDepot, $dateLimite);
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

    private function convertirDate(\DateTimeInterface|string|null $date, string $nomChamp): \DateTimeImmutable
    {
        if ($date === null || $date === '') {
            throw new \InvalidArgumentException(sprintf("Le champ '%s' est obligatoire.", $nomChamp));
        }

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
