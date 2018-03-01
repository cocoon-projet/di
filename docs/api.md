## API

Fonctions pour enregistrer les services:

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
| |Tableau associatif avec les clefs réservés @class, @constructor, @singleton, @factory, @arguments; @lazy|

singleton($alias, $service = null);

factory($alias, $callable = [], $vars = []);

lazy($class, $params = []);

addServices($services = null);

Fonction qui vérifie si un service est enregistré:

has($alias);

Fonction qui retourne l'ensemble des services enregistrés

getServices();

Fonctions pour retourner les services:

get($alias);

make($class, $mixed = null, $vars = []);