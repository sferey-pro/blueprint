# Gestion des Outils de Développement

Ce document décrit la manière dont les outils de développement sont gérés dans ce projet, ainsi qu'une présentation des outils utilisés.

## Approche : Isolation des Dépendances

Pour garantir la stabilité et éviter les conflits de dépendances ("dependency hell"), ce projet adopte une stratégie d'isolation. Les outils d'analyse et de qualité du code ne sont **pas** inclus dans le `composer.json` principal à la racine.

À la place, ils sont installés dans un répertoire dédié `tools/`, qui possède son propre `composer.json` et son propre répertoire `vendor/`.

Cette approche nous offre plusieurs avantages :

  * **🛡️ Zéro Conflit** : Les dépendances des outils ne peuvent jamais entrer en conflit avec celles de l'application.
  * **📦 Clarté** : Le `composer.json` principal ne contient que ce qui est strictement nécessaire au fonctionnement de l'application.
  * **⚙️ Reproductibilité** : Le fichier `tools/composer.lock` garantit que tous les développeurs et la CI utilisent exactement les mêmes versions des outils.

-----

## Outils Utilisés

Voici la liste des outils configurés pour ce projet.

### CaptainHook

CaptainHook est un gestionnaire de hooks Git. Il permet d'exécuter automatiquement des scripts lors d'actions Git (ex: `pre-commit`, `pre-push`) pour s'assurer que seul du code de qualité et respectant les standards est "commité".

  * **Documentation** : [https://php.captainhook.info/](https://php.captainhook.info/)
  * **Version installée** : `^5.25`
  * **Extension installée** :
      - `ramsey/conventional-commits`

-----

### Deptrac

Deptrac est un outil d'analyse statique qui permet de faire respecter les règles d'architecture logicielle. Il vérifie que les dépendances entre les différentes couches de votre application sont conformes aux règles que vous avez définies, évitant ainsi un couplage non désiré.

  * **Documentation** : [https://deptrac.github.io/deptrac/](https://deptrac.github.io/deptrac/)
  * **Version installée** : `^4.0`
  * **Commandes associées** :
      * `composer analyze:deptrac` : Lance l'analyse des dépendances architecturales.

-----

### PHP-CS-Fixer

PHP Coding Standards Fixer analyse votre code pour y trouver des erreurs de style (non-respect des standards PSR-12, etc.) et peut les corriger automatiquement. C'est un garant de l'homogénéité et de la lisibilité du code.

  * **Documentation** : [https://cs.symfony.com/](https://cs.symfony.com/)
  * **Version installée** : `^3.86`
  * **Commandes associées** :
      * `composer lint:style` : Corrige automatiquement les fichiers.

-----

### PHP Parallel Lint

Cet outil vérifie la syntaxe de tous les fichiers PHP du projet pour détecter d'éventuelles erreurs (parse errors). Il exécute cette vérification en parallèle pour être beaucoup plus rapide qu'une vérification séquentielle.

  * **Documentation** : [https://github.com/php-parallel-lint/PHP-Parallel-Lint](https://github.com/php-parallel-lint/PHP-Parallel-Lint)
  * **Version installée** : `^1.4`
  * **Commandes associées** :
      * `composer lint:syntax` : Lance la vérification de la syntaxe sur l'ensemble du projet, verfication du yaml et du container.

-----

### PHPStan

PHPStan est un analyseur statique de code qui se concentre sur la recherche de bugs sans avoir besoin d'exécuter le code. Il détecte des classes d'erreurs que les tests unitaires peuvent manquer, notamment les problèmes liés au typage.

  * **Documentation** : [https://phpstan.org/](https://phpstan.org/)
  * **Version installée** : `^2.1`
  * **Commandes associées** :
      * `composer analyze:phpstan` : Lance l'analyse statique du code.
  * **Extension installée** :
      - `phpstan/phpstan-doctrine`
      - `phpstan/phpstan-phpunit`
      - `phpstan/phpstan-symfony`
