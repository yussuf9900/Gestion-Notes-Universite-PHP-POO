<?php

declare(strict_types=1);

namespace App\Service;

class DateConverterService
{
    public static function convertir(\DateTimeInterface|string|null $date, string $nomChamp = 'date'): \DateTimeImmutable
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
