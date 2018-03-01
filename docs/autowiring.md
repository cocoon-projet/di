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