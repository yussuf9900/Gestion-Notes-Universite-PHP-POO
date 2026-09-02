<?php

declare(strict_types=1);

namespace App\Dto;

use App\Service\DateConverterService;
use App\Validator\CopieValidator;

readonly class SoumettreCopieDTO
{
    public function __construct(
        public float $noteBrute,
        public \DateTimeImmutable $dateDepot,
        public \DateTimeImmutable $dateLimite
    ) {
    }

    public static function fromArray(array $data): self
    {
        $noteBrute = $data['noteBrute'] ?? $data['note_brute'] ?? $data['note'] ?? null;
        $dateDepot = $data['dateDepot'] ?? $data['date_depot'] ?? null;
        $dateLimite = $data['dateLimite'] ?? $data['date_limite'] ?? null;

        $note = CopieValidator::validerNoteBrute($noteBrute);
        $depot = DateConverterService::convertir($dateDepot, 'date de depot');
        $limite = DateConverterService::convertir($dateLimite, 'date limite');

        return new self($note, $depot, $limite);
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
