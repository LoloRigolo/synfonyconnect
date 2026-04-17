# SymfoConnect

Réseau social minimaliste construit avec **Symfony 7.4** — posts, follows, likes, messagerie privée, fil d'actualité et API REST.

---

## Stack

| Couche | Technologie |
|--------|-------------|
| Backend | PHP 8.3 · Symfony 7.4 |
| ORM | Doctrine ORM |
| Base de données | MySQL 8.4 |
| API | API Platform 4 |
| File de messages | Symfony Messenger · Doctrine transport |
| Cache | Symfony Cache · TagAware (filesystem) |
| Email | Symfony Mailer |
| Tests | PHPUnit 12 |

---

## Prérequis

- PHP >= 8.3
- Composer
- MySQL 8.x
- (optionnel) [Mailpit](https://mailpit.axllent.org) pour intercepter les emails en dev

---

## Installation

```bash
# 1. Cloner le projet
git clone <repo-url> symfoconnect
cd symfoconnect

# 2. Installer les dépendances
composer install

# 3. Copier et adapter la configuration locale
cp .env .env.local
# Éditer .env.local : ajuster DATABASE_URL avec vos identifiants MySQL
```

### Configuration minimale dans `.env.local`

```dotenv
DATABASE_URL="mysql://user:password@127.0.0.1:3306/symfoconnect_db?serverVersion=8.4&charset=utf8mb4"
APP_SECRET=changez_cette_valeur
```

---

## Base de données

```bash
# Créer la base
symfony console doctrine:database:create

# Appliquer toutes les migrations (schéma complet)
symfony console doctrine:migrations:migrate

# Charger les données de démo
symfony console doctrine:fixtures:load
```

### Comptes de démonstration (mot de passe : `password`)

| Email | Username | Rôle |
|-------|----------|------|
| admin@symfoconnect.local | admin | ROLE_ADMIN |
| alice@symfoconnect.local | alice | ROLE_USER |
| bob@symfoconnect.local | bob | ROLE_USER |

---

## Lancer le serveur de développement

```bash
# Avec le serveur intégré Symfony (recommandé)
symfony server:start

# Ou avec PHP directement
php -S localhost:8000 -t public/
```

L'application est accessible sur **http://localhost:8000**.

---

## Fonctionnalités

| URL | Accès | Description |
|-----|-------|-------------|
| `/` | Public | Hub — liste de tous les posts |
| `/feed` | Connecté | Fil d'actualité (posts des follows) |
| `/post/nouveau` | Connecté | Créer un post |
| `/profil/{username}` | Public | Page de profil |
| `/messages` | Connecté | Liste des conversations |
| `/messages/{username}` | Connecté | Conversation privée |
| `/notifications` | Connecté | Notifications de follow |
| `/register` | Public | Inscription |
| `/login` | Public | Connexion |
| `/api` | Public | Swagger UI (documentation API) |
| `/api/posts` | Public / Connecté | API REST des posts |

---

## API REST

La documentation interactive (Swagger UI) est disponible sur **http://localhost:8000/api**.

### Endpoints

```
GET  /api/posts          Liste paginée des posts (10 par page)
GET  /api/posts/{id}     Détail d'un post
POST /api/posts          Créer un post (authentification HTTP Basic requise)
```

### Filtres disponibles

```
GET /api/posts?description=randonnée     Recherche dans le contenu (partiel)
GET /api/posts?user.username=alice       Filtre par auteur (partiel)
GET /api/posts?page=2                    Pagination
GET /api/posts?itemsPerPage=5            Taille de page (max 50)
```

### Authentification HTTP Basic

Utiliser les identifiants des comptes de démonstration :

```bash
curl -u "alice@symfoconnect.local:password" \
     -X POST http://localhost:8000/api/posts \
     -H "Content-Type: application/json" \
     -d '{"description": "Mon post via API !", "location": "Paris"}'
```

---

## Traitement asynchrone (Messenger)

L'envoi d'un message privé déclenche une **notification par email** traitée de façon asynchrone via Symfony Messenger.

### Initialiser le transport (à faire une seule fois)

```bash
symfony console messenger:setup-transports
```

Cela crée la table `messenger_messages` en base de données.

### Lancer le worker

```bash
# Consomme les messages en attente et s'arrête après 10 messages
symfony console messenger:consume async --limit=10 -vv

# Ou en mode continu (pour la production)
symfony console messenger:consume async -vv
```

### Voir les emails en développement

Par défaut, `MAILER_DSN=null://null` — les emails sont ignorés.

Pour les intercepter avec **Mailpit** :

```bash
# 1. Lancer Mailpit (interface sur http://localhost:8025)
mailpit

# 2. Modifier .env.local
MAILER_DSN=smtp://localhost:1025

# 3. Envoyer un message dans l'UI, puis lancer le worker
symfony console messenger:consume async --limit=5 -vv
# L'email apparaît dans Mailpit
```

---

## Cache du fil d'actualité

Le fil d'actualité (`/feed`) est mis en cache **5 minutes** par utilisateur via le composant Cache de Symfony avec invalidation par tags.

Le cache est **automatiquement invalidé** dès qu'un post est créé ou supprimé par un utilisateur suivi.

```bash
# Vider manuellement tout le cache applicatif
symfony console cache:clear
symfony console cache:pool:clear feed.cache
```

---

## Tests

### Configuration de la base de test

```bash
# Créer la base de données de test
symfony console doctrine:database:create --env=test

# Appliquer le schéma
symfony console doctrine:migrations:migrate --env=test --no-interaction
```

### Lancer les tests

```bash
# Tous les tests
symfonyphpunit

# Avec détail
symfonyphpunit --testdox

# Un fichier spécifique
symfonyphpunit tests/Unit/PostEntityTest.php
symfonyphpunit tests/Functional/ApiPostsTest.php
```

### Suite de tests

| Fichier | Type | Scénario |
|---------|------|----------|
| `tests/Unit/PostEntityTest.php` | Unitaire | Likes sur l'entité Post (add, remove, idempotence, compteur, createdAt) |
| `tests/Functional/PublicPageTest.php` | Fonctionnel | `GET /` retourne HTTP 200 |
| `tests/Functional/PostSecurityTest.php` | Fonctionnel | `GET /post/nouveau` sans auth → redirect `/login` |
| `tests/Functional/PostFormTest.php` | Fonctionnel | Utilisateur connecté → formulaire accessible (HTTP 200) |
| `tests/Functional/ApiPostsTest.php` | Fonctionnel | `GET /api/posts` → JSON valide avec `member` et `totalItems` |

---

## Structure du projet

```
src/
├── Controller/       # HomeController, PostController, FeedController…
├── Entity/           # User, Post, Message, Notification
├── Form/             # PostType, RegistrationFormType
├── Message/          # DTOs Messenger (NewMessageNotification)
├── MessageHandler/   # Handlers Messenger
├── Repository/       # Requêtes Doctrine
├── Security/Voter/   # PostVoter (suppression sécurisée)
├── Service/          # FeedCacheService
├── State/            # PostStateProcessor (API Platform)
└── Twig/             # NotificationExtension

templates/
├── _post_card.html.twig   # Partial carte de post (réutilisé partout)
├── base.html.twig          # Layout principal
├── feed/, home/, message/, notification/, post/, profile/…

migrations/            # 9 migrations Doctrine
tests/
├── Unit/
└── Functional/
```
