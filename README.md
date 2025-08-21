# Blueprint

**Blueprint** est un socle applicatif et un manifeste architectural en PHP. Son objectif est de fournir une base de code modulaire, testable et évolutive, conçue pour accueillir n'importe quel domaine métier.

Ce projet est une exploration pratique des principes **Domain-Driven Design (DDD)**, **CQRS** et de l'**Architecture Hexagonale**. Il sert de modèle de référence pour la construction d'applications robustes où la complexité est maîtrisée.

---

## Vision Architecturale

L'architecture de Blueprint repose sur une séparation stricte et non-négociable entre le code métier et le code technique de support. 

### `src/Business`
C'est le cœur de l'application, là où réside la logique métier. Il répond à la question : **QUE** fait l'application d'un point de vue métier ?

Ce répertoire est lui-même organisé en couches pour isoler les règles métier pures de la technologie :
* **`Domain`** : Le noyau. Il contient les règles métier, les objets et les événements qui décrivent le domaine (`Aggregates`, `Entities`, `Value Objects`). **Cette couche est 100% pure et agnostique de tout framework.**
* **`Application`** : L'orchestrateur. Il définit les cas d'utilisation de l'application (`Commands` et `Queries`) et les interfaces ("Ports") dont il a besoin (ex: `UserRepositoryInterface`).
* **`Infrastructure`** : L'implémentation. Il contient les "Adaptateurs" techniques qui implémentent les interfaces de la couche Application en utilisant des outils concrets (ex: `DoctrineUserRepository`). **C'est le seul endroit dans `src/Business` où le code du framework est autorisé et attendu.**


### `src/Kernel`
C'est la machinerie **transverse** de l'application. Il fournit les implémentations techniques concrètes (les "adaptateurs") qui permettent au métier de fonctionner. Ce répertoire contient le code de plomberie qui se connecte au framework et aux librairies externes. Il répond à la question : **COMMENT ?**

### `src/Content`
C'est une zone pragmatique pour le contenu simple (pages FAQ, CGU, etc.) qui ne nécessite pas la complexité d'un Bounded Context complet.

---

## Stack Technique & Qualité

* **Langage** : PHP 8.4+
* **Framework** : Symfony 7+
* **Qualité de Code** : La qualité est automatisée et garantie par un ensemble d'outils rigoureux :
    * **CaptainHook** : Hooks Git pour valider le code et les messages de commit avant qu'ils n'entrent dans le repository.
    * **PHP-CS-Fixer** : Respect de la norme de code PSR-12.
    * **PHPStan** : Analyse statique de code pour la détection d'erreurs.
    * **Deptrac** : Validation des dépendances architecturales pour garantir l'isolation des couches.

---

## Roadmap de Développement

### Milestone 0 : Préparation de l'Environnement

L'objectif est d'outiller notre projet pour garantir la qualité et la cohérence dès le premier commit.

* [X] **Initialisation du projet** avec symfony-docker.
* [X] **Installation et configuration de CaptainHook** pour automatiser les vérifications.
    * [X] Hook `commit-msg` pour forcer le formatage des messages (Conventional Commits).
    * [X] Hook `pre-commit` pour lancer le linter.
    * [X] Hook `pre-push` pour lancer l'analyse statique.
* [X] **Installation et configuration de PHP-CS-Fixer**, **PHPStan** et **Deptrac**.
* [X] **Création de scripts Composer** (`lint`, `analyze`) pour faciliter l'usage des outils.
* [X] **Installation de Doctrine**.
* [X] **Installation et configuration de PHPUnit**, **Zenstruck/Foundry**, **DAMA\DoctrineTestBundle** et **symfony/maker-bundle**.

