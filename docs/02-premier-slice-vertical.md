# Milestone 2 : Le Premier Slice Vertical

**Objectif :** Prouver que notre architecture fonctionne en développant une première fonctionnalité complète de bout en bout ("slice vertical"). 

Ce milestone a pour but d'établir les patrons de conception que nous réutiliserons dans toute l'application en créant un Bounded Context d'exemple : `Greeting`.

## Tâches

* [x] **Modéliser le domaine** du contexte `Greeting`.
* [x] **Implémenter le cas d'usage d'écriture** (le `Command`).
* [ ] **Implémenter le cas d'usage de lecture** (le `Query`).
* [x] **Mettre en place la persistance** avec Doctrine (l'`Adapter`).
* [ ] **Créer une interface utilisateur** pour interagir avec le contexte.

---

## Anatomie du Bounded Context `Greeting`

Le contexte `Greeting` est notre modèle de référence. Sa structure et ses composants sont le **blueprint** à suivre pour tous les autres contextes. Il est organisé selon nos trois couches architecturales.

### 1. La Couche Domaine : Le Cœur du Métier 🧠

Cette couche définit les concepts et les règles métier de notre contexte. Elle est totalement pure et agnostique de tout framework.

* **Aggregate Root (`Greeting.php`)**
    C'est l'objet métier principal. Il représente un "Greeting" et garantit la cohérence de ses données. Il contient les règles de création et de validation qui lui sont propres.

* **Value Object (`GreetingId.php`)**
    Nous encapsulons les identifiants dans des Value Objects pour garantir leur validité et apporter une sémantique forte. Un `GreetingId` n'est pas une simple `chaîne de caractères`, c'est un concept à part entière.

* **Repository Interface (`GreetingRepositoryInterface.php`)**
    C'est le **Port** de sortie de notre domaine. Il définit un contrat décrivant les besoins de persistance du domaine (ex: "j'ai besoin de pouvoir ajouter un `Greeting`"), sans imposer de technologie.

---
### 2. La Couche Application : Les Cas d'Usage 🚀

Cette couche orchestre le domaine pour répondre aux actions de l'utilisateur.

* **Command (`CreateGreetingCommand.php`)**
    C'est un objet de données (DTO) simple et immuable qui représente une **intention de modifier l'état** du système. Il transporte les informations nécessaires à l'exécution d'un cas d'usage.

* **Handler (`CreateGreetingHandler.php`)**
    C'est le chef d'orchestre du cas d'usage. Il reçoit la `Command`, utilise le `Repository` pour charger/sauvegarder des `Aggregates`, et s'appuie sur les `Factories` et les `Services de Domaine` pour exécuter la logique applicative. Il dépend des abstractions du Domaine et du Kernel (`GreetingRepositoryInterface`, `ClockInterface`).

---
### 3. La Couche Infrastructure : Les Adaptateurs Techniques 🔌

Cette couche implémente les "Ports" définis par le Domaine et l'Application en utilisant des technologies concrètes.

* **Repository Doctrine (`DoctrineGreetingRepository.php`)**
    C'est l'**Adaptateur** qui implémente notre `GreetingRepositoryInterface` en utilisant l'ORM Doctrine. C'est le seul endroit de notre contexte qui connaît l'existence de Doctrine. Nous y plaçons la logique technique pour communiquer avec la base de données.
