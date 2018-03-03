## Utiliser la classe DI (Facade du conteneur)

En utilisant cette classe, vous n'avez pas besoin d'instancier le conteneur. Vous pouvez utiliser directement toutes les méthodes de façon statique.

```php
<?php
use Cocoon\Dependency\DI;

// Enregistrer les services
DI::bind(...);
DI::singleton(...);
DI::factory(...);
DI::lazy(...);
DI::addServices(...);

// Retourner les services
DI::get(...);
DI::make(...);
```

Utilisation:

```php
<?php
use Cocoon\Dependency\DI;
use App\Repositories\ArticleRepository;
use App\Controllers\ArticlesController;

DI::addServices([
    'db.dsn' => 'mysql:host=localhost;dbname=testdb',
    'db.port' => 3306,
    'app.config' => ['mode' => 'production', 'debug' => false],
    'person' => 'App\Services\Persons',
    // alias => null   (l'alias  est le service)
    ArticleRepository::class => null,
    ArticlesController::class => ['@constructor' => [ArticleRepository::class, 'ok']]
]);

var_dump(DI::get('db.dsn')); // mysql:host=localhost;dbname=testdb
var_dump(DI::get('person')); // object(App\Services\Persons)
var_dump(DI::get(ArticlesController::class)); //object(App\Controllers\ArticlesController)
```