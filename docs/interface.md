## Interface injection
Une classe Base pour l'exemple dont le constructeur prend en paramètre une classe implémentant l'interface LoggerInterface 
```php
<?php
namespace  App\Services;

class Base
{
    public $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
```
```php
<?php
namespace  App\Services;

interface LoggerInterface
{

}
```
```php
<?php
namespace  App\Services;

class Logger implements LoggerInterface
{

}
```
Utilisation:
```php
<?php
use App\Services\Base;

$di = Container::getInstance();

// Enregistrement de l'interface
$di->bind(LoggerInterface::class, Logger::class);
// Enregistrement de la classe Base et de sa dépendance
$di->bind(Base::class, [
    '@constructor' => [LoggerInterface::class]
    ]);

// Retourner le service
$service = $di->get(Base::class);

var_dump($service instanceof App\Services\Base);
var_dump($service->logger instanceof App\Services\Logger);
```