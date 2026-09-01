# Partie 0
## 1. **`vendor/` ne doit pas être versionné**
Le dossier contient les bibliothèques installées par Composer. Il peut être très volumineux et être recréé automatiquement avec `composer install`. On versionne plutôt  ' `composer.json` et `composer.lock`.

## 2. **Commit vs Tag**
Un **commit** enregistre une modification du projet dans l’historique Git.
Un **tag** sert à identifier un commit important, généralement une version stable du projet, par exemple `v1.0.0`.

## 3. **Pourquoi `main` doit rester stable ?**
`main` représente généralement la version principale et fonctionnelle du projet. Les développeurs travaillent sur des branches comme `feature/login` ou `bugfix/paiement`, puis fusionnent leur travail dans `main` une fois qu’il est testé et fonctionnel.

**À retenir :**
> `vendor/` = dépendances, pas de versionnement.
> **Commit** = modification enregistrée.
> **Tag** = version importante identifiée.
> **main** = branche stable du projet.
