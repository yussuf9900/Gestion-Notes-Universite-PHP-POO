<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CopieExamen;

class PdoCopieExamenRepository extends Query implements CopieExamenRepositoryInterface
{
    public function __construct(\PDO $pdo)
    {
        parent::__construct($pdo);
    }

    public function save(CopieExamen $copie): CopieExamen
    {
        if ($copie->getId() === null) {
            $stmt = $this->prepare(
                'INSERT INTO copies (date_depot, date_limite, note_brute, penalite_appliquee, note_finale) VALUES (:date_depot, :date_limite, :note_brute, :penalite_appliquee, :note_finale) RETURNING id'
            );
            $stmt->execute([
                'date_depot' => $copie->getDateDepot()->format('Y-m-d H:i:s'),
                'date_limite' => $copie->getDateLimite()->format('Y-m-d H:i:s'),
                'note_brute' => $copie->getNoteBrute(),
                'penalite_appliquee' => $copie->getPenaliteAppliquee(),
                'note_finale' => $copie->getNoteFinale(),
            ]);
            $id = (int) $stmt->fetchColumn();
            $copie->setId($id);
            return $copie;
        }

        $stmt = $this->prepare(
            'UPDATE copies SET date_depot = :date_depot, date_limite = :date_limite, note_brute = :note_brute, penalite_appliquee = :penalite_appliquee, note_finale = :note_finale WHERE id = :id'
        );
        $stmt->execute([
            'id' => $copie->getId(),
            'date_depot' => $copie->getDateDepot()->format('Y-m-d H:i:s'),
            'date_limite' => $copie->getDateLimite()->format('Y-m-d H:i:s'),
            'note_brute' => $copie->getNoteBrute(),
            'penalite_appliquee' => $copie->getPenaliteAppliquee(),
            'note_finale' => $copie->getNoteFinale(),
        ]);
        return $copie;
    }

    public function findAll(): array
    {
        $rows = $this->fetchAll('SELECT * FROM copies ORDER BY id DESC');
        return array_map([$this, 'hydrate'], $rows);
    }

    public function findById(int $id): ?CopieExamen
    {
        $row = $this->fetch('SELECT * FROM copies WHERE id = :id', ['id' => $id]);
        return $row !== false ? $this->hydrate($row) : null;
    }

    private function hydrate(array $data): CopieExamen
    {
        return new CopieExamen(
            dateLimite: new \DateTimeImmutable($data['date_limite']),
            noteBrute: (float) $data['note_brute'],
            noteFinale: (float) $data['note_finale'],
            penaliteAppliquee: (float) $data['penalite_appliquee'],
            dateDepot: new \DateTimeImmutable($data['date_depot']),
            id: (int) $data['id']
        );
    }
}
