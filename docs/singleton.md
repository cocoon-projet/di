## Singleton injection

```php
<?php
namespace App\Services;

class Persons
{
    
}    
```

Maintenant il y a plusieurs moyens d'enregistrer la classe en singleton dans le container.

```php
<?php
use Cocoon\Dependency\Container;

use App\Services\Persons;

$di = Container::getInstance();  

// string alias
$di->bind('mon_singleton', ['@class' => Persons::class, '@singleton' => true]);
// ou class alias
$di->bind(Persons::class, ['@singleton' => true]);
// ou utiliser la méthode singleton du container
$di->singleton('mon_singleton', Persons::class);
// ou 
$di->singleton(Persons::class);

// Retourner le service

$service1 = $di->get('mon_singleton');
$service2 = $di->get('mon_singleton');
// ou 
$service3 = $di->get(Persons::class);
$service4 = $di->get(Persons::class);

var_dump($service1 == $service2); // true
var_dump($service3 == $service4); // true
```
> La même instance est retournée.