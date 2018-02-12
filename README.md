## A Propos

cocoon-projet/di est un conteneur d'injection de dépendance très léger et très simple à utiliser.

## Pré-requis

Php version 7.0.0 ou plus

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

echo $config['mode']; // production
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

$di->bind('ma_class', 'App\Servives\Persons');
// ou
$di->bind('ma_class', Persons::class);
// ou
$di->bind(Persons::class);

// Retourner le service

$service = $di->get('ma_class');
// ou
$service = $di->get(Persons::class);
```