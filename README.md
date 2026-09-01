# Système de Notation Universitaire (PHP 8 / PostgreSQL / POO)

Application web d'automatisation du traitement des copies d'examen, du contrôle de validité des données et du calcul dynamique des pénalités de retard selon les règlements universitaires.

---

# Partie 0 — Fondamentaux Git & Gestion du Projet

## 1. Pourquoi `vendor/` ne doit pas être versionné ?
Le dossier `vendor/` contient l'ensemble des bibliothèques tierces et fichiers générés automatiquement par Composer. Il peut être très volumineux et être recréé à l'identique à tout moment via la commande `composer install`. On versionne uniquement `composer.json` (déclaration des dépendances) et `composer.lock` (verrouillage des versions exactes).

## 2. Commit vs Tag
- Un **commit** enregistre un ensemble de modifications précises à un instant $T$ dans l'historique d'une branche.
- Un **tag** (étiquette) est un pointeur immuable sur un commit spécifique marquant un jalon important ou une version publiable (ex: `v0.1.0`, `v1.0.0`).

## 3. Pourquoi `main` doit rester stable ?
La branche `main` représente le code de production validé et directement déployable. Tous les développements s'effectuent sur des branches isolées (`partie/01-initialisation`, etc.) et ne sont fusionnés sur `main` qu'une fois testés, fonctionnels et vérifiés.

**À retenir :**
> - `vendor/` = dépendances externes & code généré, non versionné.
> - **Commit** = modification unitaire enregistrée.
> - **Tag** = version majeure / jalon identifié.
> - **main** = branche stable et prête pour le déploiement.

---

# Partie 1 — Préparation de l'Application & Architecture

## 1. Réponses aux Questions Architecturales

### Q1. Pourquoi placer `index.php` dans un dossier `public` ?
Placer le point d'entrée dans un dossier dédié `public/` répond à un impératif fondamental de **sécurité par isolation de la racine Web (*DocumentRoot*)** :
- Le serveur Web (Apache, Nginx, ou le serveur interne PHP) est configuré pour avoir pour racine exclusive le dossier `public/`.
- Tout ce qui est situé au niveau parent (`config/`, `database/`, `src/`, `templates/`, `vendor/`, ainsi que les fichiers sensibles comme `.git/` et `.env`) devient **strictement inaccessible depuis un navigateur Web**.
- Cela empêche un utilisateur malveillant de télécharger directement le code source PHP, les mots de passe de base de données ou les scripts SQL via une simple URL (ex: `http://mon-site.fr/config/database.php` ou `http://mon-site.fr/database/schema.sql`).

---

### Q2. Pourquoi toutes les requêtes devraient-elles passer par ce fichier (`public/index.php`) ?
Cette approche implémente le patron de conception **Front Controller** (Point d'Entrée Unique) et apporte des avantages déterminants :
1. **Initialisation centralisée et unique** :
   - Chargement automatique des classes via l'autoloader Composer PSR-4 (`require_once 'vendor/autoload.php'`).
   - Configuration globale de l'application et gestion uniforme des exceptions et erreurs HTTP (404, 500).
2. **Contrôle transversal et sécurité** :
   - Assainissement des données entrantes, gestion globale des sessions et sécurité applicative appliqués systématiquement avant tout traitement métier.
3. **Découplage des URL et de l'arborescence physique (Routage)** :
   - Les URL sont propres, RESTful et significatives (`/`, `/copies`, `/copie/create`), gérées dynamiquement par un **Routeur** (`App\Core\Router`) sans être dépendantes des chemins de fichiers réels sur le disque.

---

### Q3. Quels éléments ne devraient jamais se trouver dans le dossier `public` ?
Le dossier `public/` ne doit contenir **strictement que les ressources publiques statiques** et le fichier d'amorçage `index.php`. Ne doivent **JAMAIS** s'y trouver :
- Les **fichiers de configuration et mots de passe** (`config/database.php`, `.env`, clés privées, certificats).
- Le **code source applicatif et métier** (`src/` : modèles, entités, règles, validateurs, contrôleurs).
- Les **scripts de base de données** (`database/` : schémas DDL, migrations, sauvegardes SQL).
- Les **fichiers de gabarits et vues serveur** (`templates/`).
- Les **dépendances Composer** (`vendor/`, `composer.json`, `composer.lock`).
- L'**historique de versionnement** (`.git/`, `.gitignore`).

---

### Q4. Comment avez-vous réparti les responsabilités entre vos dossiers ?

L'architecture respecte strictement les principes de séparation des préoccupations (**SoC** - *Separation of Concerns*) et de responsabilité unique (**SRP** - *Single Responsibility Principle*) :

| Dossier | Responsabilité & Contenu | Justification Architecturale |
| :--- | :--- | :--- |
| **`config/`** | **Configuration isolée** :<br>• `app.php` : paramètres généraux et environnement<br>• `database.php` : identifiants et configuration PostgreSQL | Découplage fort : les informations de connexion et de configuration ne sont jamais codées en dur dans les classes métier. |
| **`database/`** | **Persistance & Schémas Relationnels** :<br>• `schema.sql` : scripts DDL (tables, contraintes, types)<br>• `migrate.php` : migrations et initialisation CLI | Isolé du Web, assure le versionnement et la reproductibilité de la structure de base de données. |
| **`public/`** | **Point d'Entrée HTTP & Assets Web** :<br>• `index.php` : Front Controller unique<br>• `css/`, `js/`, `assets/` : ressources statiques | Unique zone exposée au serveur Web (`DocumentRoot`). Reçoit toute requête entrante et délègue au routeur. |
| **`src/`** | **Code Source Applicatif (Namespace `App\`)** : | Organisation modulaire et typée respectant les principes SOLID et patrons de conception : |
| ↳ `src/Core/` | **Noyau Technique & Infrastructure** :<br>• `Router.php` : analyse d'URL et aiguillage des routes<br>• `Database.php` : gestionnaire de connexion PDO (Singleton) | Fournit les composants techniques transversaux et réutilisables nécessaires au fonctionnement applicatif. |
| ↳ `src/Entity/` | **Modèle de Domaine & Entités Métier** :<br>• `Copie.php`, `Etudiant.php`, etc. | Objets métier purs (POPO) encapsulant les données du domaine, leurs propriétés, états et comportements intrinsèques. |
| ↳ `src/Repository/` | **Accès aux Données & Persistance (DAL)** :<br>• `CopieRepository.php`, etc. | Isole l'écriture des requêtes SQL et l'accès à PostgreSQL. Fait le pont entre les tables SQL et les entités PHP (*Repository Pattern*). |
| ↳ `src/Dto/` | **Objets de Transfert de Données (DTO)** :<br>• `CopieDto.php`, etc. | Structures de données typées transportant les données entre l'interface utilisateur, les contrôleurs et la logique métier sans exposer les entités. |
| ↳ `src/Validator/` | **Validation d'Intégrité & Conformité** :<br>• `CopieValidator.php` | Contrôle la validité des données (note comprise entre 0 et 20, formats de dates, champs obligatoires, cohérence temporelle). |
| ↳ `src/Rule/` | **Règles Métier & Calculs de Pénalités** :<br>• `PenaltyRuleInterface.php`<br>• `FixedLatePenaltyRule.php`<br>• `DailyLatePenaltyRule.php`<br>• `ZeroPenaltyRule.php` | Isole le calcul dynamique des pénalités selon le **Strategy Pattern** pour garantir l'Open/Closed Principle (OCP) : ajout de règles sans modifier l'existant. |
| ↳ `src/Controller/` | **Orchestration des Flux Applicatifs** :<br>• `BaseController.php`<br>• `HomeController.php`<br>• `CopieController.php` | Réceptionne les requêtes HTTP, sollicite les validateurs/DTOs, orchestre les entités et repositories, et transmet les données aux vues. |
| **`templates/`** | **Présentation & Vues Serveur** :<br>• `layout/header.php` & `footer.php`<br>• `home/index.php`<br>• `copies/index.php`, `create.php`, `show.php` | Gabarits HTML5 / PHP purs protégés hors de la racine Web pour un rendu sécurisé. |

---

## 2. Démarrage Rapide

### 1. Démarrer le serveur de développement local
```bash
php -S localhost:8000 -t public
```
Puis accédez à [http://localhost:8000](http://localhost:8000) depuis votre navigateur.

---

## 3. Journal des Versions (Changelog)

- **`v0.0.0`** : Initialisation du dépôt Git.
- **`v0.1.0`** : **Partie 1 — Préparation de l'application & Architecture** :
  - Mise en place de l'arborescence obligatoire (`database/`, `config/`, `public/`, `src/`, `templates/`).
  - Découpage modulaire de `src/` (`Core/`, `Entity/`, `Repository/`, `Dto/`, `Validator/`, `Rule/`, `Controller/`).
  - Mise en place du Front Controller `public/index.php` et du routeur `src/Core/Router.php`.
  - Configuration de l'autoloading PSR-4 via Composer.
  - Vues d'affichage et page d'accueil avec design CSS moderne.
  - Réponses exhaustives aux 4 questions architecturales.
