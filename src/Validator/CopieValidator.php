<?php

declare(strict_types=1);

namespace App\Validator;

class CopieValidator
{
    public static function validerNoteBrute(mixed $noteBrute): float
    {
        if ($noteBrute === null || $noteBrute === '' || !is_numeric($noteBrute)) {
            throw new \InvalidArgumentException('La note brute doit etre une valeur numerique valide.');
        }

        $noteFloat = (float) $noteBrute;
        if ($noteFloat < 0.0 || $noteFloat > 20.0) {
            throw new \InvalidArgumentException(
                sprintf('La note brute doit etre comprise entre 0 et 20 (valeur recue : %.2f).', $noteFloat)
            );
        }

        return $noteFloat;
    }

    public static function validerPresence(mixed $valeur, string $nomChamp): void
    {
        if ($valeur === null || $valeur === '') {
            throw new \InvalidArgumentException(sprintf("Le champ '%s' est obligatoire.", $nomChamp));
        }
    }

    public static function validerDonnees(array $data): array
    {
        $noteBrute = $data['noteBrute'] ?? $data['note_brute'] ?? $data['note'] ?? null;
        $dateDepot = $data['dateDepot'] ?? $data['date_depot'] ?? null;
        $dateLimite = $data['dateLimite'] ?? $data['date_limite'] ?? null;

        self::validerPresence($noteBrute, 'noteBrute');
        self::validerPresence($dateDepot, 'dateDepot');
        self::validerPresence($dateLimite, 'dateLimite');

        return [
            'noteBrute' => self::validerNoteBrute($noteBrute),
            'dateDepot' => $dateDepot,
            'dateLimite' => $dateLimite,
        ];
    }
}
