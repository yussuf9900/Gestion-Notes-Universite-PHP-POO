# Journal des Versions (CHANGELOG)

## [v0.8.0] - 2026-09-02
### Partie 8 — Vues du système de notation (MVC)
- **Ajout** : Gabarits généraux de présentation `templates/layout/header.php` et `templates/layout/footer.php` (responsive, sans dépendance externe lourde).
- **Ajout** : Vues métier :
  - `templates/copies/create.php` : formulaire de soumission sécurisé en méthode HTTP `POST`, avec champs typés et affichage des messages d'erreur.
  - `templates/copies/index.php` : tableau récapitulatif des copies avec badges d'état et actions.
  - `templates/copies/show.php` : fiche détaillée d'une copie avec calcul de retard et note finale.
  - `templates/errors/404.php` et `templates/errors/error.php` : pages de signalement d'anomalies.
- **Sécurité & Architecture** :
  - Zéro requête SQL et zéro calcul de pénalité dans l'ensemble des vues.
  - Échappement contextuel systématique de toutes les données dynamiques avec `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- **Ajout** : Suite de tests automatisée `tests/test_partie8.php` (24 assertions validées).
- **Règle** : Zéro commentaire dans l'intégralité des templates HTML et PHP.

## [v0.7.0] - 2026-09-02
### Partie 7 — Service applicatif de soumission des copies
- **Ajout** : Service métier `App\Service\SoumissionCopieService` (`src/Service/SoumissionCopieService.php`).
- **Fonctionnalités** :
  - Orchestration complète : réception du `SoumettreCopieDTO`, application de la règle de calcul via `CalculNoteInterface`, enregistrement via `CopieExamenRepositoryInterface` et restitution de l'entité.
  - Découplage strict : aucune adhérence à `$_POST`, sessions ou gabarits HTML.
  - Consultation simplifiée : méthodes `listerCopies()` et `consulterCopie(int $id)`.
- **Ajout** : Suite de tests automatisée `tests/test_partie7.php` (13 assertions validées).
- **Règle** : Zéro commentaire dans l'intégralité du code source PHP.

## [v0.6.0] - 2026-09-02
### Partie 6 — Persistance des copies (Repository)
- **Ajout** : Contrat d'interface `App\Repository\CopieExamenRepositoryInterface` (`src/Repository/CopieExamenRepositoryInterface.php`).
- **Ajout** : Implémentation `App\Repository\PdoCopieExamenRepository` (`src/Repository/PdoCopieExamenRepository.php`) étendant `Query` et recevant `PDO` par constructeur.
- **Fonctionnalités** :
  - `save(CopieExamen $copie)` : persistance d'une nouvelle copie avec génération de clé primaire et mise à jour d'enregistrement existant via requêtes préparées.
  - `findAll()` et `findById(int $id)` : consultation des copies avec méthode d'hydratation factorisée (`hydrate()`).
- **Ajout** : Suite de tests automatisée `tests/test_partie6.php` (23 assertions validées).
- **Règle** : Zéro commentaire dans l'intégralité du code source PHP.

## [v0.5.0] - 2026-09-02
### Partie 5 — Stratégie de calcul des notes
- **Ajout** : Contrat `App\Service\CalculNoteInterface` (`src/Service/CalculNoteInterface.php`).
- **Ajout** : Règle de pénalité `App\Rule\ReglePenaliteInterface` et `App\Rule\ReglePenaliteFixe` (`src/Rule/ReglePenaliteFixe.php`) appliquant 2 points de pénalité en cas de retard.
- **Ajout** : Service métier `App\Service\CalculNoteAvecRetardService` (`src/Service/CalculNoteAvecRetardService.php`) garantissant un plancher à 0 pour la note finale.
- **Ajout** : Suite de tests automatisée `tests/test_partie5.php` (21 assertions validées).
- **Règle** : Zéro commentaire dans l'intégralité du code source PHP.

## [v0.4.0] - 2026-09-02
### Partie 4 — Transporter les données du formulaire
- **Ajout** : Objet de Transfert de Données `App\Dto\SoumettreCopieDTO` (`src/Dto/SoumettreCopieDTO.php`) déclaré en `readonly class` (PHP 8.2+).
- **Fonctionnalités** :
  - Transport et encapsulation immuable des données de soumission : `noteBrute`, `dateDepot`, `dateLimite`.
  - Méthode de fabrique `fromArray(array $data)` pour convertir les données du formulaire `$_POST` (chaînes $\to$ `float` et `DateTimeImmutable`).
  - Validation stricte des entrées et signalement des valeurs absentes, non numériques, hors intervalle $[0, 20]$ ou des formats de dates invalides via `InvalidArgumentException`.
- **Ajout** : Suite de tests automatisée `tests/test_partie4.php` (22 assertions validées).
- **Documentation** : Réponses exhaustives aux 4 questions théoriques sur les DTO, la séparation avec `$_POST`, la distinction avec `CopieExamen` et la frontière de conversion des dates dans `README.md`.
- **Règle** : Zéro commentaire dans l'intégralité du code source PHP.

## [v0.3.1] - 2026-09-02
### Évolution de la Couche Repository & Gestion des Requêtes
- **Ajout** : Dépendance `vlucas/phpdotenv` et migration complète vers `$_ENV` dans `config/database.php` (abandon de `getenv()`).
- **Refactorisation** : Déplacement de `Database` vers `App\Repository\Database` (`src/Repository/Database.php`) pour centraliser la couche d'accès aux données.
- **Ajout** : Classe `App\Repository\Query` (`src/Repository/Query.php`) offrant des méthodes d'exécution de requêtes sécurisées (`prepare`, `query`, `executeQuery`, `fetchAll`, `fetch`, `lastInsertId`, transactions).
- **Mise à jour** : `database/init.php` et `tests/test_partie3.php` adaptés pour exploiter `App\Repository\Database` et `App\Repository\Query`.
- **Règle** : Zéro commentaire dans l'intégralité du code source PHP et SQL.

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
