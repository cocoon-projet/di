# API Cocoon DI

Cette documentation détaille l'ensemble des méthodes disponibles dans le conteneur d'injection de dépendances Cocoon DI.

## Table des matières

- [Méthodes d'enregistrement des services](#méthodes-denregistrement-des-services)
  - [bind()](#bind)
  - [singleton()](#singleton)
  - [factory()](#factory)
  - [lazy()](#lazy)
  - [addServices()](#addservices)
- [Méthodes de vérification](#méthodes-de-vérification)
  - [has()](#has)
  - [getServices()](#getservices)
- [Méthodes de récupération](#méthodes-de-récupération)
  - [get()](#get)
  - [make()](#make)

## Méthodes d'enregistrement des services

### bind()

Enregistre un service dans le conteneur avec différentes options de configuration.

```php
public function bind(string|object $alias, mixed $service = null): void
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$alias` | `string` ou `object` | Identifiant du service (alias ou nom de classe) |
| `$service` | `mixed` | Service à enregistrer (optionnel) |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Enregistrement d'une valeur simple
$container->bind('db.dsn', 'mysql:host=localhost;dbname=test');

// Enregistrement d'un tableau
$container->bind('app.config', [
    'env' => 'production',
    'debug' => false
]);

// Enregistrement d'une classe
$container->bind(UserService::class);

// Enregistrement avec injection par constructeur
$container->bind(UserService::class, [
    '@class' => UserService::class,
    '@constructor' => [LoggerInterface::class]
]);

// Enregistrement avec injection par méthodes
$container->bind(UserService::class, [
    '@class' => UserService::class,
    '@methods' => [
        'setConfig' => [['debug' => true]],
        'setLogger' => [LoggerInterface::class]
    ]
]);

// Enregistrement en tant que singleton
$container->bind(UserService::class, [
    '@class' => UserService::class,
    '@singleton' => true
]);

// Enregistrement avec factory
$container->bind(UserService::class, [
    '@factory' => [UserFactory::class, 'createUser']
]);

// Enregistrement en mode lazy
$container->bind(UserService::class, [
    '@lazy' => true,
    '@constructor' => [LoggerInterface::class]
]);
```

### singleton()

Enregistre un service en tant que singleton. Le conteneur retournera toujours la même instance.

```php
public function singleton(string|object $alias, mixed $service = null): void
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$alias` | `string` ou `object` | Identifiant du service |
| `$service` | `mixed` | Service à enregistrer (optionnel) |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Enregistrement d'un singleton
$container->singleton('logger', Logger::class);

// Enregistrement d'un singleton avec alias
$container->singleton('app.logger', Logger::class);

// Enregistrement d'un singleton par nom de classe
$container->singleton(Logger::class);
```

### factory()

Enregistre un service en utilisant une factory pour sa création.

```php
public function factory(string|object $alias, array $callable = [], array $vars = []): void
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$alias` | `string` ou `object` | Identifiant du service |
| `$callable` | `array` | Tableau contenant la classe factory et la méthode à appeler |
| `$vars` | `array` | Arguments à passer à la méthode factory |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Enregistrement avec factory simple
$container->factory(UserService::class, [
    UserFactory::class,
    'createUser'
]);

// Enregistrement avec factory et arguments
$container->factory(UserService::class, [
    UserFactory::class,
    'createUser'
], ['arg1', 'arg2']);
```

### lazy()

Enregistre un service en mode lazy loading.

```php
public function lazy(string|object $class, array $params = []): void
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$class` | `string` ou `object` | Classe à charger en mode lazy |
| `$params` | `array` | Arguments pour le constructeur |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Enregistrement en mode lazy
$container->lazy(UserService::class);

// Enregistrement en mode lazy avec arguments
$container->lazy(UserService::class, [
    'name' => 'John',
    'email' => 'john@example.com'
]);
```

### addServices()

Enregistre plusieurs services à partir d'un tableau ou d'un fichier de configuration.

```php
public function addServices(array|string $services = null): void
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$services` | `array` ou `string` | Tableau de services ou chemin vers un fichier de configuration |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Enregistrement à partir d'un tableau
$container->addServices([
    'db.dsn' => 'mysql:host=localhost;dbname=test',
    'app.config' => [
        'env' => 'production',
        'debug' => false
    ],
    UserService::class => [
        '@class' => UserService::class,
        '@singleton' => true
    ]
]);

// Enregistrement à partir d'un fichier
$container->addServices('config/services.php');
```

## Méthodes de vérification

### has()

Vérifie si un service est enregistré dans le conteneur.

```php
public function has(string|object $alias): bool
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$alias` | `string` ou `object` | Identifiant du service à vérifier |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

if ($container->has('logger')) {
    $logger = $container->get('logger');
    $logger->info('Service trouvé');
}
```

### getServices()

Retourne tous les services enregistrés dans le conteneur.

```php
public function getServices(): array
```

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Récupération de tous les services
$services = $container->getServices();

// Affichage des services
print_r($services);
```

## Méthodes de récupération

### get()

Récupère un service enregistré dans le conteneur.

```php
public function get(string|object $alias): mixed
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$alias` | `string` ou `object` | Identifiant du service à récupérer |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Récupération d'un service
$logger = $container->get('logger');

// Récupération par nom de classe
$userService = $container->get(UserService::class);
```

### make()

Crée une nouvelle instance d'une classe avec l'autowiring.

```php
public function make(string|object $class, mixed $mixed = null, array $vars = []): object
```

#### Paramètres

| Paramètre | Type | Description |
|-----------|------|-------------|
| `$class` | `string` ou `object` | Classe à instancier |
| `$mixed` | `mixed` | Arguments du constructeur ou nom d'une méthode |
| `$vars` | `array` | Arguments pour la méthode spécifiée |

#### Exemples d'utilisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Création simple avec autowiring
$userService = $container->make(UserService::class);

// Création avec arguments du constructeur
$userService = $container->make(UserService::class, [
    'name' => 'John',
    'email' => 'john@example.com'
]);

// Création et appel d'une méthode
$user = $container->make(UserService::class, 'createUser', [
    'name' => 'John',
    'email' => 'john@example.com'
]);
```
