## API

> Fonctions pour enregistrer les services:

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
| |Tableau associatif avec les clefs réservées @class, @constructor, @singleton, @factory, @arguments; @lazy|
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
  
```
singleton($alias, $service = null);

factory($alias, $callable = [], $vars = []);

lazy($class, $params = []);

addServices($services = null);

> Fonction qui vérifie si un service est enregistré:

has($alias);

> Fonction qui retourne l'ensemble des services enregistrés

getServices();

> Fonctions pour retourner les services:

get($alias);

make($class, $mixed = null, $vars = []);