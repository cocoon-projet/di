## Constructeur injection

Imaginons un controller Article qui a besoin de récupérer les articles d'une Classe ArticleRepository
et avec un autre paramètre pour l'exemple.
```php
<?php

namespace App\Controllers;

use App\Repositories\ArticleRepository;

class ArticlesController
{
    public $repository;
    
    public $otherParam;
    
    public function __construct(ArticleRepository $repository, $otherParam)
    {
        $this->repository = $repository;
        $this->otherParam = $otherParam;
    }
}
```
La classe ArticleRepository

```php
<?php

namespace App\Repositories;

class ArticleRepository
{
    
}
```
Enregistrement du service

```php
<?php
use Cocoon\Dependency\Container;
use App\Repositories\ArticleRepository;
use App\Controllers\ArticlesController;

$di = Container::getInstance();

// Enregistrement de la classe ArticleRepository
$di->bind(ArticleRepository::class);
// Enregistrement de la classe ArticlesController et ses dépendances
$di->bind(ArticlesController::class, ['@constructor' => [ArticleRepository::class, 'ok']]);

$service = $di->get(ArticlesController::class);

var_dump($service instanceof  App\Controllers\ArticlesController); // true
var_dump($service->repository instanceof App\Repositories\ArticleRepository); //true
var_dump($service->otherParam === 'ok'); // true
```