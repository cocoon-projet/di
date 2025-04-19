# Pattern Factory avec Cocoon DI

Le pattern Factory permet de déléguer la création d'objets à une classe spécialisée, offrant ainsi plus de flexibilité et de contrôle sur le processus d'instanciation.

## Définition des classes

### Classe à instancier

```php
<?php
declare(strict_types=1);

namespace App\Services;

class User
{
    private string $name;
    private string $email;
    
    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
}
```

### Différents types de Factory

#### Factory classique

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserFactory
{
    public function createUser(string $name, string $email): User
    {
        return new User($name, $email);
    }
}
```

#### Factory statique

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserStaticFactory
{
    public static function createUser(string $name, string $email): User
    {
        return new User($name, $email);
    }
}
```

#### Factory avec __invoke

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserInvokeFactory
{
    public function __invoke(string $name, string $email): User
    {
        return new User($name, $email);
    }
}
```

## Utilisation avec le conteneur

### Méthode 1 : Utilisation de bind() avec @factory

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\{User, UserFactory, UserStaticFactory, UserInvokeFactory};

$container = Container::getInstance();

// Factory classique
$container->bind(User::class, [
    '@factory' => [UserFactory::class, 'createUser']
]);

// Factory statique
$container->bind(User::class, [
    '@factory' => [UserStaticFactory::class, 'createUser']
]);

// Factory avec __invoke
$container->bind(User::class, [
    '@factory' => [UserInvokeFactory::class]
]);

// Récupération de l'instance
$user = $container->get(User::class);
```

### Méthode 2 : Utilisation de la méthode factory()

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\{User, UserFactory};

$container = Container::getInstance();

// Enregistrement de la factory
$container->factory(User::class, [UserFactory::class, 'createUser']);

// Récupération de l'instance
$user = $container->get(User::class);
```

## Passage d'arguments à la factory

### Avec bind()

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\{User, UserFactory};

$container = Container::getInstance();

$container->bind(User::class, [
    '@factory' => [UserFactory::class, 'createUser'],
    '@arguments' => ['John Doe', 'john@example.com']
]);

$user = $container->get(User::class);
```

### Avec factory()

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\{User, UserFactory};

$container = Container::getInstance();

$container->factory(
    User::class,
    [UserFactory::class, 'createUser'],
    ['John Doe', 'john@example.com']
);

$user = $container->get(User::class);
```

## Bonnes pratiques

1. Utilisez des factories pour :
   - La création d'objets complexes
   - L'encapsulation de la logique de création
   - La gestion des dépendances de création
   - La validation des données avant la création

2. Nommez vos méthodes de factory de manière descriptive :
   - `createUser()`
   - `buildUser()`
   - `makeUser()`

3. Utilisez le typage strict pour les paramètres et les retours

4. Documentez les paramètres attendus et les valeurs de retour

## Exemple complet

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserFactory
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function createUser(string $name, string $email): User
    {
        $this->logger->info("Création d'un nouvel utilisateur : $name");
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email invalide : $email");
        }
        
        return new User($name, $email);
    }
}

// Utilisation
$container->bind(User::class, [
    '@factory' => [UserFactory::class, 'createUser'],
    '@arguments' => ['John Doe', 'john@example.com']
]);
```