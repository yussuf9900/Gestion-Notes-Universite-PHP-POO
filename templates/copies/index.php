<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1>Liste des copies d'examen</h1>
        <a href="/copies/create" class="btn">+ Soumettre une copie</a>
    </div>

    <?php if (empty($copies)): ?>
        <p style="color: #64748b; font-style: italic;">Aucune copie d'examen n'a été enregistrée pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date de dépôt</th>
                    <th>Date limite</th>
                    <th>Statut</th>
                    <th>Note brute</th>
                    <th>Pénalité</th>
                    <th>Note finale</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($copies as $copie): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars((string) $copie->getId(), ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td><?= htmlspecialchars($copie->getDateDepot()->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($copie->getDateLimite()->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($copie->isEnRetard()): ?>
                                <span class="badge badge-danger">En retard</span>
                            <?php else: ?>
                                <span class="badge badge-success">À l'heure</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(number_format($copie->getNoteBrute(), 2), ENT_QUOTES, 'UTF-8') ?>/20</td>
                        <td>
                            <?php if ($copie->getPenaliteAppliquee() > 0): ?>
                                <span style="color: var(--danger); font-weight: 600;">-<?= htmlspecialchars(number_format($copie->getPenaliteAppliquee(), 2), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php else: ?>
                                <span style="color: var(--success);">0.00</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars(number_format($copie->getNoteFinale(), 2), ENT_QUOTES, 'UTF-8') ?>/20</strong></td>
                        <td>
                            <a href="/copies/<?= htmlspecialchars((string) $copie->getId(), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.35rem 0.65rem;">Détails</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
