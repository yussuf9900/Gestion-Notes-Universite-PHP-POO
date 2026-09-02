<div class="card">
    <h1>Soumettre une nouvelle copie</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Des erreurs ont été détectées :</strong>
            <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/copies">
        <div class="form-group">
            <label for="noteBrute">Note brute de l'examen (entre 0 et 20) :</label>
            <input type="number" step="0.01" min="0" max="20" id="noteBrute" name="noteBrute" required value="<?= htmlspecialchars((string) ($old['noteBrute'] ?? $old['note_brute'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="dateDepot">Date et heure effectives de dépôt :</label>
            <input type="datetime-local" id="dateDepot" name="dateDepot" required value="<?= htmlspecialchars((string) ($old['dateDepot'] ?? $old['date_depot'] ?? date('Y-m-d\TH:i')), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="dateLimite">Date et heure limites autorisées :</label>
            <input type="datetime-local" id="dateLimite" name="dateLimite" required value="<?= htmlspecialchars((string) ($old['dateLimite'] ?? $old['date_limite'] ?? date('Y-m-d\TH:i')), ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
            <button type="submit" class="btn">Enregistrer et Calculer</button>
            <a href="/copies" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
