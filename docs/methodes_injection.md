## Méthodes injection

Vous pouvez aussi injecter des paramètres vie les méthodes.
```php
<?php
namespace App\Services;

class A 
{
    public $b;
    public $param;
    
    public function setB(B $b)
    {
        $this->b = $b;
    }
    
    public function setParam($param)
    {
        $this->param = $param;
    }
}
```

```php
<?php
namespace App\Services;

class B 
{

}
```
Utilisation
```php
<?php
$di = Container::getInstance();

// enregistrement de la class B
$di->bind(B::class);
// enregistrement de la classe A et ses dépendances via les méthodes
$di->bind(A::class, ['@methods' => [
                            'setB' => [B::class],
                            'setParam' => ['je suis un paramètre']
                            ]
                    ]);

// Retourner le service
$service = $di->get(A::class);

var_dump($service instanceof App\Services\A); // true
var_dump($service->b instanceof App\Services\B); // true
var_dump($service->param === 'je suis un paramètre'); // true
```