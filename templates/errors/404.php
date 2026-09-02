<div class="card" style="text-align: center; padding: 3rem 1.5rem;">
    <h1 style="font-size: 3rem; color: var(--danger); margin-bottom: 0.5rem;">404</h1>
    <h2>Page non trouvée</h2>
    <p style="color: #64748b; margin-top: 1rem; margin-bottom: 2rem;">
        <?= htmlspecialchars($message ?? 'La page ou la copie d\'examen demandée n\'existe pas.', ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="/copies" class="btn">Retour à la liste des copies</a>
</div>
