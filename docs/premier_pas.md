## Premier pas

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();
```
#### string, integer, array injection

```php
$di->bind('db.dsn', 'mysql:host=localhost;dbname=testdb');

$di->bind('db.port', 3306);

$di->bind('app.config', ['mode' => 'production', 'debug' => false]);
```
#### Retourner les services

```php
$dsn = $di->get('db.dsn'); // mysql:host=localhost;dbname=testdb

$port = $di->get('db.port'); // 3306

$config = $di->get('app.config'); // array('mode' => 'production', 'debug' => false)

var_dump($dsn == 'mysql:host=localhost;dbname=testdb'); // true
var_dump($port == 3306); // true
var_dump($config['mode'] == 'production'); // true
```
#### object injection

```php
<?php
namespace App\Services;

class Persons
{
    
}    
```
Maintenant il y a plusieurs moyens d'enregistrer la classe dans le container.

```php
<?php
use Cocoon\Dependency\Container;

use App\Services\Persons;

$di = Container::getInstance();

$di->bind('ma_class', 'App\Services\Persons');
// ou
$di->bind('ma_class', Persons::class);
// ou
$di->bind(Persons::class);

// Retourner le service

$service1 = $di->get('ma_class');
$service2 = $di->get('ma_class');
// ou
$service3 = $di->get(Persons::class);
$service4 = $di->get(Persons::class);

var_dump($service1 === $service2); // false
var_dump($service3 === $service4); // false
```
> Une nouvelle instance de la classe est retournée à chaque appel.