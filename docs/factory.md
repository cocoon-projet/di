## Factory injection

Vous pouvez instancier une classe via une autre classe et une méthode ou une méthode statique ou une classe avec la méthode magique __invoke

```php
<?php
namespace App\Services;
         
class Item 
{
    
}
```
Les classes Factory

```php
<?php
namespace App\Services;

class ItemFactory
{
    public function create()
    {
        return new Item();
    }
}
//ou
class ItemStaticFactory
{
    public static function create()
    {
        return new Item();
    }
}

//ou

class ItemInvokeFactory
{
    public function __invoke()
    {
        return new Item();
    }
}
```

Utilisation

```php
<?php
use Cocoon\Dependency\Container;
use App\Services\Item;
use App\Services\ItemFactory;
use App\Services\ItemStaticFactory;
use App\Services\ItemInvokeFactory;

$di = Container::getInstance();

$di->bind(Item::class, ['@factory' => [ItemFactory::class, 'create']]);
// ou $di->bind(Item::class, ['@factory' => [ItemStaticFactory::class, 'create']]);
// ou $di->bind(Item::class, ['@factory' => [ItemInvokeFactory::class]]);

$service = $di->get(Item::class);

var_dump($service instanceof App\Services\Item); //true
```
Vous obtenez la même résultat avec le code suivant

```php
<?php
use Cocoon\Dependency\Container;
use App\Services\Item;
use App\Services\ItemFactory;

$di = Container::getInstance();

$di->bind('item.factory', [
    '@factory' => [ItemFactory::class, 'create']]);

$service = $di->get('item.factory');

var_dump($service instanceof App\Services\Item); //true
```

Vous pouvez aussi utiliser la méthode factory du container

```php
<?php
use Cocoon\Dependency\Container;
use App\Services\Item;
use App\Services\ItemFactory;

$di = Container::getInstance();

$di->factory(Item::class, [ItemFactory::class, 'create']);

$service = $di->get(Item::class);

var_dump($service instanceof App\Services\Item); // true
```
> Note: Il est possible d'injecter des arguments suplémentaires à la fonction appelée.

Vous pouvez procéder de la manière suivante

```php
<?php

$di->bind('class.factory', [
    '@factory' => [MaclassFactory::class, 'create'], 
    '@arguments' => ['arg1', 'arg2']
    ]);
// ou

$di->factory('class.factory', [MaClassFactory::class, 'create'], ['arg1', 'arg2']);
```