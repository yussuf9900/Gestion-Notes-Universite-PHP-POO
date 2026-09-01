<?php

declare(strict_types=1);

namespace App\Core;


abstract class AbstractDocument
{
  
    protected ?int $id = null;

   
    protected \DateTimeImmutable $dateDepot;

  
    public function __construct(
        \DateTimeInterface|string $dateDepot = new DateTimeImmutable(),
        ?int $id = null
    ) {
        $this->setId($id);
        $this->setDateDepot($dateDepot);
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

   
    public function setId(?int $id): static
    {
        if ($id !== null && $id <= 0) {
            throw new \InvalidArgumentException("L'identifiant du document doit être un entier strictement positif.");
        }
        $this->id = $id;
        return $this;
    }

    
    public function getDateDepot(): \DateTimeImmutable
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface|string $dateDepot): static
    {
        if (is_string($dateDepot)) {
            try {
                $dateDepot = new \DateTimeImmutable($dateDepot);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException("Format de date de dépôt invalide : " . $e->getMessage());
            }
        } elseif ($dateDepot instanceof \DateTime) {
            $dateDepot = \DateTimeImmutable::createFromMutable($dateDepot);
        }

        $this->dateDepot = $dateDepot;
        return $this;
    }
}
