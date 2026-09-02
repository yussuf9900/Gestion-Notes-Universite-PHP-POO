<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\SoumettreCopieDTO;
use App\Entity\CopieExamen;
use App\Repository\CopieExamenRepositoryInterface;

class SoumissionCopieService
{
    private CopieExamenRepositoryInterface $repository;
    private CalculNoteInterface $calculNoteService;

    public function __construct(
        CopieExamenRepositoryInterface $repository,
        ?CalculNoteInterface $calculNoteService = null
    ) {
        $this->repository = $repository;
        $this->calculNoteService = $calculNoteService ?? new CalculNoteAvecRetardService();
    }

    public function soumettre(SoumettreCopieDTO $dto): CopieExamen
    {
        $copie = new CopieExamen(
            dateLimite: $dto->getDateLimite(),
            noteBrute: $dto->getNoteBrute(),
            noteFinale: $dto->getNoteBrute(),
            penaliteAppliquee: 0.0,
            dateDepot: $dto->getDateDepot()
        );

        $this->calculNoteService->calculer($copie);

        return $this->repository->save($copie);
    }

    public function listerCopies(): array
    {
        return $this->repository->findAll();
    }

    public function consulterCopie(int $id): ?CopieExamen
    {
        return $this->repository->findById($id);
    }

    public function getRepository(): CopieExamenRepositoryInterface
    {
        return $this->repository;
    }

    public function getCalculNoteService(): CalculNoteInterface
    {
        return $this->calculNoteService;
    }
}
