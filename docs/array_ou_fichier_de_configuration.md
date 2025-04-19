# Configuration du conteneur avec un tableau ou un fichier

Le conteneur Cocoon DI permet de configurer les services de deux manières : via un tableau PHP ou via un fichier de configuration.

## Configuration via un tableau

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Configuration des services via un tableau
$container->addServices([
    // Configuration de la base de données
    'db.dsn' => 'mysql:host=localhost;dbname=testdb',
    'db.port' => 3306,
    
    // Configuration de l'application
    'app.config' => [
        'mode' => 'production',
        'debug' => false,
        'timezone' => 'Europe/Paris'
    ],
    
    // Services
    Logger::class => [
        '@class' => Logger::class,
        '@singleton' => true
    ],
    
    // Repository avec injection de dépendances
    ArticleRepository::class => [
        '@class' => ArticleRepository::class,
        '@constructor' => ['db.dsn', 'db.port']
    ],
    
    // Controller avec injection de dépendances
    ArticlesController::class => [
        '@class' => ArticlesController::class,
        '@constructor' => [ArticleRepository::class],
        '@methods' => [
            'setLogger' => [Logger::class]
        ]
    ]
]);

// Utilisation des services
$dsn = $container->get('db.dsn');
$config = $container->get('app.config');
$controller = $container->get(ArticlesController::class);

// Utilisation de make() pour créer une nouvelle instance
$newController = $container->make(ArticlesController::class);

// Utilisation de autowire() pour résoudre automatiquement les dépendances
$service = $container->autowire(SomeService::class);
```

## Configuration via un fichier

### 1. Création du fichier de configuration

```php
<?php
// config/services.php
declare(strict_types=1);

return [
    // Configuration de la base de données
    'db.dsn' => 'mysql:host=localhost;dbname=testdb',
    'db.port' => 3306,
    
    // Configuration de l'application
    'app.config' => [
        'mode' => 'production',
        'debug' => false,
        'timezone' => 'Europe/Paris'
    ],
    
    // Services
    Logger::class => [
        '@class' => Logger::class,
        '@singleton' => true
    ],
    
    // Repository avec injection de dépendances
    ArticleRepository::class => [
        '@class' => ArticleRepository::class,
        '@constructor' => ['db.dsn', 'db.port']
    ],
    
    // Controller avec injection de dépendances
    ArticlesController::class => [
        '@class' => ArticlesController::class,
        '@constructor' => [ArticleRepository::class],
        '@methods' => [
            'setLogger' => [Logger::class]
        ]
    ]
];
```

### 2. Chargement de la configuration

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Chargement de la configuration depuis un fichier
$container->addServices(require 'config/services.php');

// Utilisation des services
$dsn = $container->get('db.dsn');
$config = $container->get('app.config');
$controller = $container->get(ArticlesController::class);

// Utilisation de make() pour créer une nouvelle instance
$newController = $container->make(ArticlesController::class);

// Utilisation de autowire() pour résoudre automatiquement les dépendances
$service = $container->autowire(SomeService::class);
```

## Différences entre get(), make() et autowire()

1. **get()** :
   - Retourne une instance singleton si configuré
   - Utilise la configuration définie
   - Peut retourner la même instance à chaque appel

2. **make()** :
   - Crée toujours une nouvelle instance
   - Utilise la configuration définie
   - Utile pour les objets qui doivent être uniques

3. **autowire()** :
   - Crée une nouvelle instance
   - Résout automatiquement les dépendances
   - Ignore la configuration existante
   - Utile pour les tests ou les cas spéciaux

## Bonnes pratiques

1. **Organisation des configurations** :
   - Séparer les configurations par environnement
   - Grouper les services par fonctionnalité
   - Utiliser des alias significatifs

2. **Utilisation des méthodes** :
   - Préférer `get()` pour les services partagés
   - Utiliser `make()` pour les objets uniques
   - Employer `autowire()` pour les tests ou les cas spéciaux

3. **Structure recommandée** :
   ```
   config/
   ├── services.php
   ├── services.dev.php
   ├── services.prod.php
   └── services.test.php
   ```

## Exemple complet avec les trois méthodes

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\{Logger, UserService, MailService};

$container = Container::getInstance();

// Configuration
$container->addServices([
    Logger::class => [
        '@class' => Logger::class,
        '@singleton' => true
    ],
    UserService::class => [
        '@class' => UserService::class,
        '@constructor' => [Logger::class]
    ]
]);

// Utilisation de get() pour un service partagé
$logger1 = $container->get(Logger::class);
$logger2 = $container->get(Logger::class);
var_dump($logger1 === $logger2); // true

// Utilisation de make() pour une nouvelle instance
$userService1 = $container->make(UserService::class);
$userService2 = $container->make(UserService::class);
var_dump($userService1 === $userService2); // false

// Utilisation de autowire() pour une résolution automatique
$mailService = $container->autowire(MailService::class);
// Les dépendances sont résolues automatiquement
```
