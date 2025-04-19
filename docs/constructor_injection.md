# Injection par Constructeur

L'injection par constructeur est la méthode recommandée pour l'injection de dépendances.

## Utilisation Simple

```php
<?php
declare(strict_types=1);

class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly LoggerInterface $logger,
        private readonly array $config = []
    ) {}
}

// Autowiring automatique
$container->make(UserService::class);

// Configuration explicite
$container->bind(UserService::class, [
    '@class' => UserService::class,
    '@constructor' => [
        'repository' => UserRepository::class,
        'logger' => LoggerInterface::class,
        'config' => ['cache' => true]
    ]
]);
```

## Fonctionnalités PHP 8.x

### Promotion des Propriétés

```php
class ProductService
{
    public function __construct(
        private readonly ProductRepository $repository,
        private readonly EventDispatcher $dispatcher,
        private readonly ?CacheInterface $cache = null
    ) {}
}
```

### Types Union

```php
class LogService
{
    public function __construct(
        private LoggerInterface|NullLogger $logger,
        private string|array $config = []
    ) {}
}
```

### Types Intersection (PHP 8.1+)

```php
class OrderProcessor
{
    public function __construct(
        private readonly (HasId&HasTotal) $order,
        private readonly PaymentGateway $gateway
    ) {}
}
```

## Bonnes Pratiques

1. Utilisez la promotion des propriétés
2. Déclarez les propriétés comme readonly quand possible
3. Utilisez le typage strict
4. Documentez les paramètres complexes
5. Limitez le nombre de dépendances (max 3-4)
6. Utilisez des interfaces plutôt que des implémentations