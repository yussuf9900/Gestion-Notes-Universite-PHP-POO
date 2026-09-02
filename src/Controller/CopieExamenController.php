<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\SoumettreCopieDTO;
use App\Service\SoumissionCopieService;

class CopieExamenController extends BaseController
{
    private SoumissionCopieService $service;

    public function __construct(SoumissionCopieService $service)
    {
        $this->service = $service;
    }

    public function index(): void
    {
        $copies = $this->service->listerCopies();
        $this->render('copies/index', ['copies' => $copies]);
    }

    public function create(array $errors = [], array $old = []): void
    {
        $this->render('copies/create', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function store(?array $requestData = null): void
    {
        $data = $requestData ?? $_POST;

        try {
            $dto = SoumettreCopieDTO::fromArray($data);
            $copie = $this->service->soumettre($dto);
            $this->redirect('/copies/' . $copie->getId());
        } catch (\InvalidArgumentException $e) {
            $this->render('copies/create', [
                'errors' => [$e->getMessage()],
                'old' => $data,
            ], 400);
        }
    }

    public function show(int $id): void
    {
        $copie = $this->service->consulterCopie($id);
        if ($copie === null) {
            $this->render('errors/404', [
                'message' => 'La copie demandee n\'a pas ete trouvee.',
            ], 404);
            return;
        }

        $this->render('copies/show', ['copie' => $copie]);
    }

    public function getService(): SoumissionCopieService
    {
        return $this->service;
    }
}
