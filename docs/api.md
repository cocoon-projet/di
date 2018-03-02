## API

> Méthodes pour enregistrer les services:

bind($alias, $service = null);

|$alias|$services|
|--------|---------|
|string 'mon.alias'  |null| 
|résolution de nom de classe User::class|int| 
|Chemin complet d'une classe 'App\Controllers\User'|string| 
| |array   | 
| |callable   | 
| |résolution de nom de classe User::class|
| |Chemin complet d'une classe 'App\Controllers\User'|
| |Tableau associatif avec les clefs réservées @class, @constructor, @methods, @singleton, @factory, @arguments; @lazy|

Exemple:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();
// string
$di->bind('mon.alias', 'je suis une chaîne de caractère');
// int
$di->bind('mon.numero', 156250);
// array 
$di->bind('mon.tableau', ['db' => 'mysql', 'port' => 3600]);
// callable
$di->bind('app.user', function() {
    return new User();
});
//  string alias et résolution de nom de classe
$di->bind('app.user', User::class);
//  string alias et chemin complet de classe
$di->bind('app.user', 'App\Controllers\User');
// Enregistrement Résolution de nom de classe
$di->bind(User::class);
// Enregistrement avec le chemin complet de classe
$di->bind('App\Controllers\User');
// string alias et constructor injection
$di->bind('app.user', ['@class' => User::class,
                       '@constructor' => ['arg1', 'arg2']
                       ]);
// méthodes injection avec la clef réservée @methods
$di->bind('app.user', ['@class' => User::class,
                       '@methods' => ['setName' => ['Doe'],
                                      'setSurName' => ['John']
                                      ]
                       ]);
//  singleton injection
$di->bind('app.user', ['@class' => User::class,
                       '@singleton' => true
                       ]);
// ou
$di->bind(User::class, ['@singleton' => true]);
// factory injection
$di->bind(User::class, [
    '@factory' => [UserFactory::class, 'getUser']
    ]);
// factory injection avec arguments dans la méthode
$di->bind(User::class, [
      '@factory' => [UserFactory::class, 'getUser'],
      '@arguments' => ['arg1', 'arg2']
      ]); 
// lazy injection
$di->bind(User::class, ['@lazy' => true]);
// lazy injection avec paramètres dans le constructeur de la classe
$di->bind(User::class, [
    '@lazy' => true,
    '@constructor' => ['arg1', 'arg2']
    ]);
```
singleton($alias, $service = null);

|$alias|$services|
|-------|-------------|
|string 'mon.singleton'  |null| 
|résolution de nom de classe MaClasse::class|résolution de nom de classe MaClasse::class| 
|Chemin complet d'une classe 'App\Controllers\Maclass'|Chemin complet d'une classe 'App\Controllers\Maclass'| 

Exemple:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

$di->singleton('mon.singleton', 'App\Controllers\Maclass');
// ou
$di->singleton('mon.singleton', Maclass::class);
//ou
$di->singleton('App\Controllers\Maclass');
// ou
$di->singleton(Maclass::class);
```

factory($alias, $callable = [], $vars = []);

|$alias|$callable|$vars|
|-------|-------------|------------|
|string 'mon.factory'  |Array [MaclasseFactory::class, 'getMaClasse']| Array Arguments ['arg1', 'arg2'] |
|résolution de nom de classe MaClasse::class||
|Chemin complet d'une classe 'App\Controller\Maclasse| 

Exemple:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

$di->factory('mon.factory', [MaClasseFactory::class, 'getMaclasse']);
// ou
$di->factory('mon.factory', ['App\Factory\MaClasseFactory', 'getMaclasse']);
// ou 
$di->factory(MaClasse::class, [MaClasseFactory::class, 'getMaclasse']);
// Si la méthode de la classe factory a des arguments
$di->factory(MaClasse::class, [MaClasseFactory::class, 'getMaclasse'], ['arg1', 'arg2']);

```

lazy($class, $params = []);

|$class|$params|
|-------|-------------|
|résolution de nom de classe MaClasse::class|Array tableau d'arguments pour le constructeur ['arg1, 'arg2'] |
|Chemin complet d'une classe 'App\Controller\Maclasse| 

Exemple:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

$di->lazy(MaClasse::class);
// ou
$di->lazy('App\Services\MaClasse');
// si le constructeur a des arguments
$di->lazy(MaClasse::class, ['arg1', 'arg2']);
```

addServices($services = null);

$services|
|-------|
|Array (un tableau de services ou un fichier qui retourne une tableau de services)|

Exemple:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

// Tableau de services  [ alias => services ]
$di->addServices([
    'database' => 'mysql',
    'config' => ['mode' => 'dev', 'debug' => true],
    'app.user' => User::class,
    'request' => [
        '@class' => Request::class,
        '@singleton' => true
    ]
]);

// ou un fichier qui retourne un tableau de services
$di->addServices(require 'config.php');
```

> Méthode qui vérifie si un service est enregistré:

has($alias);

Utilisation:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

$di->bind('service', 'mon service');
// on vérifie si le service éxiste
if ($di->has('service')) {
    return $di->get('service');
}

```

> Méthode qui retourne l'ensemble des services enregistrés

getServices();

Utilisation:

```php
<?php
use Cocoon\Dependency\Container;

$di = Container::getInstance();

// Tableau de services  [ alias => services ]
$di->addServices([
    'database' => 'mysql',
    'config' => ['mode' => 'dev', 'debug' => true],
    'app.user' => User::class,
    'request' => [
        '@class' => Request::class,
        '@singleton' => true
    ]
]);
// Retourne les services enregistrés
var_dump($di->getServices());
```

> Méthodes pour retourner les services:

get($alias);

make($class, $mixed = null, $vars = []);