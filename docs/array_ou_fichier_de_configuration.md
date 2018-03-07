## Enregistrer les services à partir d'un tableau php ou un fichier de configuration

Utilisation: Via un tableau php

```php
<?php
use Cocoon\Dependency\Container;
use App\Repositories\ArticleRepository;
use App\Controllers\ArticlesController;

$di = Container::getInstance();

// array [ $alias => $services ]
$di->addServices([
    'db.dsn' => 'mysql:host=localhost;dbname=testdb',
    'db.port' => 3306,
    'app.config' => ['mode' => 'production', 'debug' => false],
    'person' => 'App\Services\Persons',
    // alias => null   (l'alias  est le service)
    ArticleRepository::class => null,
    ArticlesController::class => ['@constructor' => [ArticleRepository::class, 'ok']]
]);
```

Utilisation: Via un fichier de configuration

le fichier config.php
```php
<?php
use App\Repositories\ArticleRepository;
use App\Controllers\ArticlesController;

return[
    'db.dsn' => 'mysql:host=localhost;dbname=testdb',
    'db.port' => 3306,
    'app.config' => ['mode' => 'production', 'debug' => false],
    'person' => 'App\Services\Persons',
    // alias => null   (l'alias  est le service)
    ArticleRepository::class => null,
    ArticlesController::class => ['@constructor' => [ArticleRepository::class, 'ok']]
];

// Vous pouvez maintenant retourner les services avec la méthode get() du conteneur
```
Insertion du fichier de configuration

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

// array [ $alias => $services ]
$di->addServices('config.php');

// Vous pouvez maintenant retourner les services avec la méthode get() du conteneur
```
