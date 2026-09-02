# Journal des Versions (CHANGELOG)

## [v0.3.0] - 2026-09-02
### Partie 3 — Préparer la persistance
- **Ajout** : Script DDL `database/schema.sql` définissant la table `copies` avec contraintes d'intégrité `CHECK` sur les notes et pénalités.
- **Ajout** : Données d'essai insérées dans PostgreSQL (copies avec et sans retard).
- **Ajout** : Gestion sécurisée des identifiants avec `.env.example`, `.env` exclu par `.gitignore`, et `config/database.php`.
- **Ajout** : Classe technique `App\Database\Database` implémentant le pattern Singleton PDO.
- **Ajout** : Script d'initialisation CLI `database/init.php`.
- **Ajout** : Suite de tests automatisée `tests/test_partie3.php`.
- **Documentation** : Réponses exhaustives aux 4 questions théoriques sur la persistance, PDO et la sécurité des identifiants dans `README.md`.
- **Règle** : Zéro commentaire dans l'intégralité du code source PHP et SQL.

## [v0.2.0] - 2026-09-01
### Partie 2 — Représenter les documents universitaires
- **Ajout** : Classe abstraite `App\Entity\AbstractDocument` avec encapsulation de l'identifiant nullable et de la date de dépôt.
- **Ajout** : Entité concrète `App\Entity\CopieExamen` avec validation des notes $[0, 20]$ et calcul du retard.
- **Documentation** : Réponses aux 4 questions conceptuelles (Héritage, Abstraction, Nullabilité de l'ID, Encapsulation).

## [v0.1.0] - 2026-09-01
### Partie 1 — Préparation de l'application & Architecture
- **Ajout** : Arborescence modulaire (`config/`, `database/`, `public/`, `src/`, `templates/`).
- **Ajout** : Point d'entrée unique `public/index.php`.
- **Ajout** : Configuration de l'autoloader PSR-4 (`composer.json`).
- **Documentation** : Réponses aux 4 questions architecturales.

## [v0.0.0] - 2026-09-01
- **Initialisation** : Initialisation du dépôt Git du projet.
