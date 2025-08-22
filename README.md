<p align="center"><a href="https://github.com/sferey-pro/blueprint" target="_blank">
    <img src="https://github.com/sferey-pro/blueprint/blob/main/docs/logos/blueprint-logo-subtitle.png" alt="Blueprint Logo" width="500">
</a></p>

**Blueprint** est un socle applicatif et un manifeste architectural en PHP. Son objectif est de fournir une base de code modulaire, testable et évolutive, conçue pour accueillir n'importe quel domaine métier.

Ce projet est une exploration pratique des principes **Domain-Driven Design (DDD)**, **CQRS** et de l'**Architecture Hexagonale**. Il sert de modèle de référence pour la construction d'applications robustes où la complexité est maîtrisée.

---

## Architecture du projet

L'architecture de Blueprint repose sur une séparation stricte et non-négociable entre le code métier et le code technique de support.

[Vision Architecturale][5]

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

- [Préparation de l'Environnement][00]
- [Le Socle Applicatif][01]



[1]: #
[2]: https://github.com/sferey-pro/blueprint

[5]: /docs/ARCHITECTURE.md
[00]: /docs/00-preparation-environnement.md
[01]: /docs/01-socle-applicatif.md

