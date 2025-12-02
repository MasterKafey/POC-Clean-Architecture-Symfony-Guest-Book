
# POC - Clean Architecture & DDD - Symfony Guest Book

Ce projet est un Proof Of Concept visant à implémenter une architecture propre et découplée autour de Symfony, en appliquant les principes :

- Clean Architecture
- Domain-Driven Design (DDD)
- Hexagonal Architecture (Ports & Adapters)
- CQRS basique (Commands / Results)

L’objectif est de créer un système de Guest Book robuste, testable et évolutif.

---

## Fonctionnalités

### Authentication
- Inscription d’utilisateur
- Connexion via authenticator Symfony
- Génération de token d’authentification persistant
- Bannissement / débannissement d’utilisateur

### Guest Book
- Création de commentaire
- Mise à jour et suppression
- Règles d’accès centralisées (policies)

---

## Architecture

Le projet est structuré par modules métier :

```txt
src/
├─ Authentication/
└─ Comment/
```

Chaque module suit la même organisation interne :

```txt
<Module>/
├─ Domain
│  ├─ Entity
│  ├─ ValueObject
│  ├─ Policy
│  ├─ Event
│  └─ Repository (interfaces)
├─ Application
│  └─ UseCase (Command, Handler, Result)
└─ Infrastructure
   ├─ Symfony (Controller, Authenticator)
   ├─ Repository (implémentations Doctrine)
   └─ Mapper
```

---

## Domain

Contient :

- Entités métier (`User`, `Token`, `Comment`)
- Value Objects (`Email`, `FirstName`, `LastName`, etc.)
- Domain Events
- Règles et invariants métier
- Exceptions métier

Objectifs :
- cohérence du modèle métier
- absence totale de technologie
- "immutabilité" lorsque possible

---

## Application

Chaque fonctionnalité est définie par un **Use Case**

Exemple :

```
RegisterUserCommand
RegisterUserHandler
RegisterUserResult
```

Un Handler :

- valide la requête métier
- récupère les données via les repositories du Domain
- applique la logique métier
- persiste via un port adapté
- retourne un résultat structuré

Jamais :
- de doctrine
- d’HTTP
- de configuration technique

---

##  Infrastructure

Ce module sert à adapter le monde extérieur vers l’application.

Il contient :

### Persistence Doctrine
- Entities dédiées Doctrine
- Repositories implémentant les interfaces Domain
- Mapping Domain ↔ Doctrine

### Controllers
- conversion Request → DTO Input
- appel du UseCase
- transformation Output → JSON

### Authentication Symfony
- Custom Authenticators
- Passport + Token generation

### Adapters
- `UuidGeneratorPort` → implémentation avec `symfony/uid`
- `PasswordHasherPort` → wrapper autour du hasher Symfony

---

## Installation

### 1. Installation des dépendances

```
composer install
```

### 2. Configuration `.env.local`

Exemple PostgreSQL :

```
DATABASE_URL="postgresql://user:password@localhost:5432/guestbook?serverVersion=16"
```

### 3. Initialisation de la base de données

```
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Lancement

```
symfony serve
```

---

## Endpoints principaux

### Auth
```
POST /user/register
POST /user/login
POST /user/ban/{id}
POST /user/unban/{id}
```

### Commentaires
```
POST /comment
PATCH /comment/{id}
DELETE /comment/{id}
GET /comment
```

---

## Tests

Différents tests développés pour s'assurer du besoin métier de l'application :

- Tests unitaires sur Value Objects (validation)
- Tests unitaires sur Policies
- Tests applicatifs sur UseCases
- Tests d’intégration sur Doctrine (round-trip)


---

## Ce que démontre ce POC

- Comment isoler le modèle métier
- Comment ne pas mélanger Doctrine et Domain
- Comment structurer des cas d’usage clairs
- Comment créer des adapters techniques
- Comment rendre un projet Symfony testable facilement
- Comment intégrer la séparation hexagonale au routing et controllers Symfony
