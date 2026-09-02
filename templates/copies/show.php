<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1>Détail de la copie #<?= htmlspecialchars((string) $copie->getId(), ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/copies" class="btn btn-secondary">&larr; Retour à la liste</a>
    </div>

    <div class="details-grid">
        <div class="detail-item">
            <div class="detail-label">Statut de soumission</div>
            <div class="detail-value">
                <?php if ($copie->isEnRetard()): ?>
                    <span class="badge badge-danger">En retard</span>
                <?php else: ?>
                    <span class="badge badge-success">À l'heure</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Date effective de dépôt</div>
            <div class="detail-value" style="font-size: 1rem;">
                <?= htmlspecialchars($copie->getDateDepot()->format('d/m/Y H:i:s'), ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Date limite autorisée</div>
            <div class="detail-value" style="font-size: 1rem;">
                <?= htmlspecialchars($copie->getDateLimite()->format('d/m/Y H:i:s'), ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Retard constaté</div>
            <div class="detail-value" style="font-size: 1rem;">
                <?php if ($copie->isEnRetard()): ?>
                    <?= htmlspecialchars((string) $copie->calculerRetardJours(), ENT_QUOTES, 'UTF-8') ?> jour(s) de dépassement
                <?php else: ?>
                    Aucun retard
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Note brute attribuée</div>
            <div class="detail-value" style="color: #1e3a8a;">
                <?= htmlspecialchars(number_format($copie->getNoteBrute(), 2), ENT_QUOTES, 'UTF-8') ?> / 20
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">Pénalité de retard appliquée</div>
            <div class="detail-value" style="color: var(--danger);">
                -<?= htmlspecialchars(number_format($copie->getPenaliteAppliquee(), 2), ENT_QUOTES, 'UTF-8') ?> point(s)
            </div>
        </div>

        <div class="detail-item" style="grid-column: 1 / -1; background-color: #eff6ff; border-color: #bfdbfe;">
            <div class="detail-label" style="color: #1e40af;">Note Finale Homologuée</div>
            <div class="detail-value" style="font-size: 2rem; color: #1e3a8a;">
                <?= htmlspecialchars(number_format($copie->getNoteFinale(), 2), ENT_QUOTES, 'UTF-8') ?> / 20
            </div>
        </div>
    </div>
</div>
