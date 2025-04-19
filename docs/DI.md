# Utilisation de la Facade DI

La classe `DI` est une Facade qui simplifie l'utilisation du conteneur d'injection de dépendances en fournissant un accès statique à toutes ses fonctionnalités.

## Avantages de la Facade

- Accès simplifié aux méthodes du conteneur
- Pas besoin d'instancier le conteneur
- Code plus concis et lisible
- Compatible avec toutes les fonctionnalités du conteneur

## Méthodes disponibles

### Enregistrement des services

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\DI;

// Enregistrement basique
DI::bind('service.name', Service::class);

// Enregistrement en singleton
DI::singleton('logger', Logger::class);

// Enregistrement avec factory
DI::factory(User::class, [UserFactory::class, 'createUser']);

// Enregistrement en mode lazy
DI::lazy(HeavyService::class);

// Enregistrement multiple
DI::addServices([
    'db.dsn' => 'mysql:host=localhost;dbname=test',
    'app.config' => ['env' => 'production']
]);
```

### Récupération des services

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\DI;

// Récupération d'un service
$service = DI::get('service.name');

// Création avec autowiring
$instance = DI::make(UserService::class);
```

## Exemple complet

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\DI;
use App\Repositories\ArticleRepository;
use App\Controllers\ArticlesController;
use App\Services\Logger;

// Configuration de base
DI::addServices([
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
$dsn = DI::get('db.dsn');
$config = DI::get('app.config');
$controller = DI::get(ArticlesController::class);
```

## Bonnes pratiques

1. Utilisez la Facade pour :
   - Les configurations globales
   - Les services partagés
   - Les tests rapides
   - Les prototypes

2. Évitez la Facade pour :
   - Les applications complexes nécessitant plusieurs conteneurs
   - Les cas où vous avez besoin de contrôler le cycle de vie du conteneur
   - Les tests unitaires (préférez l'injection directe)

3. Organisation recommandée :
   ```php
   <?php
   declare(strict_types=1);
   
   namespace App\Config;
   
   use Cocoon\Dependency\DI;
   
   class Services
   {
       public static function register(): void
       {
           DI::addServices([
               // Configuration des services
           ]);
       }
   }
   
   // Dans votre point d'entrée
   Services::register();
   ```

## Limitations

- La Facade utilise un conteneur singleton en interne
- Pas de support pour plusieurs instances de conteneur
- Moins flexible que l'utilisation directe du conteneur

## Migration depuis le conteneur

Si vous utilisez actuellement le conteneur directement :

```php
<?php
// Avant
$container = Container::getInstance();
$container->bind('service', Service::class);
$service = $container->get('service');

// Après
DI::bind('service', Service::class);
$service = DI::get('service');
```