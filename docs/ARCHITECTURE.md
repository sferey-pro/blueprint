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


## Structure cible dans src/ :

```
src/
├── Business/
│   ├── Contexts/
│   └── Shared/
├── Content/
└── Kernel/
```
