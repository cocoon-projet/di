## A Propos

cocoon-projet/di est un conteneur d'injection de dépendance très léger et très simple à utiliser.

## Pré-requis

Php version 7.0.0 ou plus

## Installation
via composer:

     composer require "cocoon-projet/di"

inclure la dépendance `cocoon-projet/di` dans votre fichier composer.json

```json
{
    "require": {
        "cocoon-projet/di": "1.0.*"
    }
}
```

## Documentation
La documentation complète est disponible sur [http://www.cocoon-projet.fr/components/di](http://www.cocoon-projet.fr/components/di).

## Premier pas

```php
use Cocoon\Dependency\Container;
$di = Container::getInstance();
```