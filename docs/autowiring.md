# Autowiring avec Cocoon DI

L'autowiring est une fonctionnalité puissante qui permet au conteneur de résoudre automatiquement les dépendances d'une classe en analysant son constructeur et ses méthodes.

## Principe de base

Le conteneur peut automatiquement :
1. Détecter les dépendances dans le constructeur
2. Résoudre les dépendances en fonction de leur type
3. Créer les instances nécessaires

## Exemple de base

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserService
{
    private LoggerInterface $logger;
    private UserRepository $repository;
    
    public function __construct(LoggerInterface $logger, UserRepository $repository)
    {
        $this->logger = $logger;
        $this->repository = $repository;
    }
    
    public function createUser(string $username): void
    {
        $this->logger->info("Création de l'utilisateur: $username");
        $this->repository->save(new User($username));
    }
}
```

## Utilisation de l'autowiring

### Méthode 1 : Via la méthode autowire()

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\UserService;

$container = Container::getInstance();

// Résolution automatique des dépendances
$userService = $container->autowire(UserService::class);

// Les dépendances sont automatiquement résolues
$userService->createUser('john_doe');
```

### Méthode 2 : Via la méthode make()

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\UserService;

$container = Container::getInstance();

// Création d'une nouvelle instance avec résolution des dépendances
$userService = $container->make(UserService::class);

// Les dépendances sont résolues selon la configuration
$userService->createUser('john_doe');
```

## Différences entre autowire() et make()

1. **autowire()** :
   - Ignore la configuration existante
   - Résout toutes les dépendances automatiquement
   - Utile pour les tests ou les cas spéciaux
   - Crée toujours une nouvelle instance

2. **make()** :
   - Utilise la configuration existante
   - Peut utiliser des services préconfigurés
   - Plus flexible pour la production
   - Crée toujours une nouvelle instance

## Exemple complet avec les deux méthodes

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\{UserService, Logger, DatabaseLogger};

$container = Container::getInstance();

// Configuration d'un service
$container->bind(Logger::class, DatabaseLogger::class);

// Utilisation de make() - utilise la configuration
$userService1 = $container->make(UserService::class);
// Logger sera une instance de DatabaseLogger

// Utilisation de autowire() - ignore la configuration
$userService2 = $container->autowire(UserService::class);
// Logger sera une nouvelle instance de Logger
```

## Bonnes pratiques

1. **Utilisation de make()** :
   - Pour la production
   - Quand vous avez une configuration spécifique
   - Pour les services qui nécessitent une configuration particulière

2. **Utilisation de autowire()** :
   - Pour les tests unitaires
   - Pour les prototypes
   - Quand vous voulez ignorer la configuration existante

3. **Organisation du code** :
   - Utilisez le typage strict pour les paramètres
   - Documentez les dépendances avec des commentaires PHPDoc
   - Évitez les dépendances circulaires

## Exemple avancé avec plusieurs dépendances

```php
<?php
declare(strict_types=1);

namespace App\Services;

class OrderService
{
    private LoggerInterface $logger;
    private PaymentProcessor $payment;
    private OrderRepository $repository;
    private NotificationService $notifier;
    
    public function __construct(
        LoggerInterface $logger,
        PaymentProcessor $payment,
        OrderRepository $repository,
        NotificationService $notifier
    ) {
        $this->logger = $logger;
        $this->payment = $payment;
        $this->repository = $repository;
        $this->notifier = $notifier;
    }
    
    public function processOrder(Order $order): void
    {
        $this->logger->info("Traitement de la commande #{$order->getId()}");
        
        try {
            $this->payment->process($order);
            $this->repository->save($order);
            $this->notifier->sendConfirmation($order);
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors du traitement: " . $e->getMessage());
            throw $e;
        }
    }
}

// Utilisation
$container = Container::getInstance();

// Toutes les dépendances sont automatiquement résolues
$orderService = $container->autowire(OrderService::class);
$orderService->processOrder($order);
```

## Limitations et considérations

1. **Dépendances circulaires** :
   - L'autowiring ne peut pas résoudre les dépendances circulaires
   - Utilisez des interfaces ou des setters dans ce cas

2. **Types primitifs** :
   - Les paramètres primitifs (string, int, etc.) ne peuvent pas être résolus automatiquement
   - Utilisez la configuration ou des valeurs par défaut

3. **Interfaces multiples** :
   - Si plusieurs classes implémentent la même interface, utilisez la configuration pour spécifier laquelle utiliser
