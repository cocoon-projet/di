# Injection par Attributs

> Cette fonctionnalité permet d'utiliser les attributs PHP 8 pour l'injection de dépendances.

## Installation

Cette fonctionnalité est disponible nativement dans le container et ne nécessite aucune installation supplémentaire.

## Utilisation

### Injection par Type

L'injection par type utilise le type de la propriété pour résoudre la dépendance :

```php
<?php
use Cocoon\Dependency\Features\Attributes\Inject;

class UserService {
    #[Inject]
    private LoggerInterface $logger;
}
```

### Injection par Nom de Service

L'injection par nom de service utilise un identifiant spécifique pour résoudre la dépendance :

```php
<?php
use Cocoon\Dependency\Features\Attributes\Inject;

class UserService {
    #[Inject('custom.logger')]
    private LoggerInterface $logger;
}
```

### Injection dans le Constructeur

Les attributs peuvent également être utilisés sur les paramètres du constructeur :

```php
<?php
use Cocoon\Dependency\Features\Attributes\Inject;

class UserService {
    public function __construct(
        #[Inject]
        private UserRepository $repository,
        #[Inject('custom.logger')]
        private LoggerInterface $logger
    ) {}
}
```

## Exemple Complet

```php
<?php
use Cocoon\Dependency\Container;
use Cocoon\Dependency\Features\Attributes\Inject;

// Interfaces et classes
interface LoggerInterface {
    public function log(string $message): void;
}

class Logger implements LoggerInterface {
    public function log(string $message): void {
        echo $message;
    }
}

interface UserRepositoryInterface {
    public function find(int $id): array;
}

class UserRepository implements UserRepositoryInterface {
    public function find(int $id): array {
        return ['id' => $id, 'name' => 'John Doe'];
    }
}

// Service utilisant l'injection par attributs
class UserService {
    #[Inject]
    private LoggerInterface $logger;

    #[Inject('custom.logger')]
    private LoggerInterface $customLogger;

    public function __construct(
        #[Inject]
        private UserRepositoryInterface $repository
    ) {}

    public function getUser(int $id): array {
        $this->logger->log("Recherche de l'utilisateur $id");
        $this->customLogger->log("Recherche personnalisée de l'utilisateur $id");
        return $this->repository->find($id);
    }
}

// Configuration et utilisation
$container = Container::getInstance();

// Approche 1: Utilisation de bind()
$container->bind(UserService::class, [
    '@inject' => true,
    '@class' => UserService::class,
    '@constructor' => [
        'repository' => UserRepositoryInterface::class
    ]
]);

$container->bind(LoggerInterface::class, Logger::class);
$container->bind('custom.logger', Logger::class);
$container->bind(UserRepositoryInterface::class, UserRepository::class);

// Approche 2: Utilisation de inject() et with() (recommandée)
$container->inject(UserService::class, [UserRepositoryInterface::class])
    ->with([
        [LoggerInterface::class => Logger::class],
        ['custom.logger' => Logger::class],
        [UserRepositoryInterface::class => UserRepository::class]
    ]);

// Utilisation
$userService = $container->get(UserService::class);
$user = $userService->getUser(1);
```

## Gestion des Erreurs

Le container lève des exceptions dans les cas suivants :

1. Service non défini :
```php
// Erreur : Le service LoggerInterface n'est pas défini
$container->get(UserService::class);
```

2. Type non défini :
```php
class InvalidService {
    #[Inject]
    private $undefined; // Erreur : Le type de la propriété undefined n'est pas défini
}
```

3. Paramètre sans valeur par défaut :
```php
class InvalidService {
    public function __construct(
        #[Inject]
        private UserRepositoryInterface $repository,
        private string $name // Erreur : Le paramètre name n'a pas de valeur par défaut
    ) {}
}
```

## Bonnes Pratiques

1. Utilisez des interfaces pour les dépendances :
```php
#[Inject]
private LoggerInterface $logger; // ✅ Bon
#[Inject]
private Logger $logger; // ❌ À éviter
```

2. Privilégiez l'injection par type :
```php
#[Inject]
private LoggerInterface $logger; // ✅ Bon
#[Inject('logger')]
private LoggerInterface $logger; // ❌ À éviter sauf cas spécifique
```

3. Documentez vos services :
```php
/**
 * Service de gestion des utilisateurs
 */
class UserService {
    /**
     * @var LoggerInterface Logger principal
     */
    #[Inject]
    private LoggerInterface $logger;
}
```

4. Utilisez des noms de service explicites si nécessaire :
```php
#[Inject('database.primary')]
private DatabaseInterface $primaryDb;

#[Inject('database.secondary')]
private DatabaseInterface $secondaryDb;
```

5. Privilégiez l'approche avec `inject()` et `with()` pour une meilleure lisibilité :
```php
// ✅ Bon : Configuration claire et lisible
$container->inject(UserService::class, [UserRepositoryInterface::class])
    ->with([
        [LoggerInterface::class => Logger::class],
        ['custom.logger' => Logger::class],
        [UserRepositoryInterface::class => UserRepository::class]
    ]);

// ❌ À éviter : Configuration moins lisible
$container->bind(UserService::class, [
    '@inject' => true,
    '@class' => UserService::class,
    '@constructor' => [
        'repository' => UserRepositoryInterface::class
    ]
]);
``` 