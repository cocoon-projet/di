## Autowiring

L'autowiring se définit comme le système d'injection de dépendances automatique réalisé par le conteneur. Les objets sont créés automatiquements.

> Dans cocoon-projet/di ce mécanisme est géré avec la fonction make()

Exemple

```php
<?php
namespace  App\Services;

class Bim
{
    public $bam;
    public $boum;
    
    public function __construct(Bam $bam, Boum $boum)
    {
        $this->bam = $bam;
        $this->boum = $boum;
    }
} 
```

```php
<?php
namespace  App\Services;

class Bam
{
    public $bum;
    
    public function __construct(Bum $bum)
    {
        $this->bum = $bum;
    }
} 
```
```php
<?php
namespace  App\Services;

class Boum
{

} 
```
```php
<?php
namespace  App\Services;

class Bum
{

} 
```
Utilisation
```php
<?php
use Cocoon\Dependency\Container;
use App\Services\Bim;

$di = Container::getInstance();

$service = $di->make(Bim::class);

var_dump($service instanceof App\Services\Bim); // true
var_dump($service->bam instanceof App\Services\Bam); // true
var_dump($service->boum instanceof App\Services\Boum); // true
var_dump($service->bam->bum instanceof App\Services\Bum); // true
```

Ajouter des arguments dans le constructeur

Exemple:

```php
<?php

class BlogController
{
    private $articles;
    private $level;
    
    public function __construct(Articles $articles, $level) 
    {
        $this->articles = $articles;
        $this->level = $level;
    }
}

// Utilisation
$di = Container::getInstance();

$service = $di->make(BlogController::class, ['level' => 'débutant']);

```

Renvoyer le résultat d'une méthode de la classe autowiré avec des arguments ou des objets dans la méthode

Exemple:

```php
<?php

class BlogController
{
    private $articles;
    
    public function __construct(Articles $articles) 
    {
        $this->articles = $articles;
    }
    
    public function show($id)
    {
        return $this->articles->find($id);
    }
}

// Utilisation
$di = Container::getInstance();

$service = $di->make(BlogController::class, 'show', ['id' => 15]);
```

> Note: Vous pouvez injecter les objets dans les méthodes

Refactorisation de BlogControlleur

```php
<?php

class BlogController
{    
    public function __construct() 
    {
    }
    
    public function show(Articles $articles, $id)
    {
        return $articles->find($id);
    }
}

// Utilisation
$di = Container::getInstance();

$service = $di->make(BlogController::class, 'show', ['id' => 15]);
```
Vous obtenez le même résultat.

> Note: Si la classe appelée par la méthode make du conteneur contient des objets enregistrés dans le conteneur, ils seront injectés automatiquements dans cette classe.

Exemple:

```php
<?php

class BlogController
{   
    private $pdo;
    
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function show($id)
    {
        $result = $this->pdo->query("SELECT * FROM posts WHERE id=" . $id)->fetchAll();
        return $result[0];
    }
}
```

Fonctionnement:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

$di->bind(PDO::class, [
    '@constructor' => ['sqlite:database.sqlite']
    ]);

$service = $di->make(BlogController::class, 'show', ['id' => 15]);
```

Cette utilisation est importante quand les objets injectés ont des paramètres (ex: PDO avec son dsn)?
