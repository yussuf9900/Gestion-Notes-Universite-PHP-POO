<div class="card" style="text-align: center; padding: 3rem 1.5rem;">
    <h1 style="color: var(--danger); margin-bottom: 0.5rem;">Une erreur est survenue</h1>
    <p style="color: #64748b; margin-top: 1rem; margin-bottom: 2rem;">
        <?= htmlspecialchars($message ?? 'Une anomalie a été rencontrée lors du traitement de votre demande.', ENT_QUOTES, 'UTF-8') ?>
    </p>
    <a href="/copies" class="btn">Retour à la liste des copies</a>
</div>
