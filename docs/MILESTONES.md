# Project Milestones

Ce document définit les étapes de développement et les objectifs techniques du projet.

---

### ✅ **Milestone 0 : Environment & Tooling**

**Objectif : Mettre en place un environnement de développement standardisé et automatisé.**

*   **Environnement de Développement :** Conteneurisé via Docker pour une reproductibilité totale.
*   **Qualité de Code :** Intégration de PHP-CS-Fixer (style) et PHPStan (analyse statique) pour garantir la conformité et la robustesse du code.
*   **Conformité Git :** Utilisation de hooks Git (CaptainHook) pour automatiser la validation des standards (Conventional Commits, linters) avant chaque commit et push.
*   **Outillage :** Scripts Composer et intégration de Symfony CLI pour l'optimisation des tâches de développement.

---

### ✅ **Milestone 1 : Architectural Foundation**

**Objectif : Établir les fondations techniques et les patrons de conception du projet.**

*   **Structure DDD :** Implémentation de la structure des répertoires et validation des dépendances entre couches via Deptrac.
*   **Bus CQRS :** Mise en œuvre des bus de Commandes, Requêtes et Événements.
*   **Atomicité des Commandes :** Intégration d'un middleware transactionnel pour garantir l'atomicité de l'exécution des cas d'usage.
*   **Shared Kernel :** Développement des composants de domaine transverses (`AggregateRoot`, abstractions `Clock` et `UUID`).
*   **Stratégie de Test :** Configuration de l'environnement de test (PHPUnit, Foundry, DAMA) pour les tests unitaires, d'intégration et fonctionnels.

---

### ✅ **Milestone 2 : First Vertical Slice**

**Objectif : Valider l'architecture par l'implémentation d'une première fonctionnalité complète de bout en bout.**

*   **Bounded Context `Greeting` :** Développement d'un contexte métier de référence, incluant :
    *   **Modèle de Domaine :** Agrégat `Greeting` avec son cycle de vie (`DRAFT`, `PUBLISHED`) et ses Value Objects.
    *   **Cas d'Usage (CQRS) :** Implémentation des commandes (`CreateGreeting`, `PublishGreeting`) et des requêtes (`ListGreetings`).
    *   **Couche de Persistance :** Implémentation du repository avec Doctrine, incluant les mappings et les types custom.
    *   **Interfaces Externes :** Exposition des fonctionnalités via une API REST et des commandes CLI.
    *   **Notifications Temps Réel :** Diffusion des événements de domaine via un push Mercure.

---
