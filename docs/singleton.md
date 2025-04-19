# Singletons dans Cocoon DI

## Définition d'un Singleton

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

// Méthode simple
$container->singleton(Database::class);

// Avec une classe différente
$container->singleton(DatabaseInterface::class, MySQLDatabase::class);

// Configuration complète
$container->singleton(Cache::class, [
    '@class' => RedisCache::class,
    '@constructor' => [
        'host' => 'localhost',
        'port' => 6379
    ]
]);

// Via configuration array
$container->addServices([
    'db' => [
        '@class' => Database::class,
        '@singleton' => true,
        '@constructor' => [
            'config' => ['host' => 'localhost']
        ]
    ]
]);
```

## Utilisation avec PHP 8.x

```php
// Utilisation avec readonly (PHP 8.1+)
readonly class Configuration
{
    public function __construct(
        public string $env,
        public array $options
    ) {}
}

$container->singleton(Configuration::class, [
    '@constructor' => [
        'env' => 'prod',
        'options' => ['debug' => false]
    ]
]);

// Utilisation avec les énumérations (PHP 8.1+)
enum Environment
{
    case DEVELOPMENT;
    case PRODUCTION;
    case TESTING;
}

$container->singleton(Environment::class, Environment::PRODUCTION);
```

## Bonnes pratiques

1. Limitez l'utilisation des singletons aux cas nécessaires
2. Préférez l'injection de dépendances standard quand possible
3. Utilisez les singletons pour les ressources partagées (DB, Cache, Config)
4. Documentez l'utilisation des singletons
5. Testez la réutilisation correcte des instances