# Premiers pas avec Cocoon DI

Ce guide vous aidera à démarrer rapidement avec Cocoon DI, un conteneur d'injection de dépendances moderne pour PHP 8.

## Installation

```bash
composer require cocoon-projet/di
```

## Initialisation

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

// Récupération de l'instance unique du conteneur
$container = Container::getInstance();
```

## Injection de valeurs simples

Cocoon DI permet d'injecter facilement des valeurs simples comme des chaînes de caractères, des entiers ou des tableaux.

```php
// Injection d'une chaîne de caractères (DSN de connexion)
$container->bind('db.dsn', 'mysql:host=localhost;dbname=testdb');

// Injection d'un entier (port de la base de données)
$container->bind('db.port', 3306);

// Injection d'un tableau (configuration de l'application)
$container->bind('app.config', [
    'mode' => 'production',
    'debug' => false,
    'timezone' => 'Europe/Paris'
]);
```

## Récupération des services

```php
// Récupération des services injectés
$dsn = $container->get('db.dsn');        // 'mysql:host=localhost;dbname=testdb'
$port = $container->get('db.port');      // 3306
$config = $container->get('app.config'); // ['mode' => 'production', ...]

// Vérification des valeurs
var_dump($dsn === 'mysql:host=localhost;dbname=testdb'); // true
var_dump($port === 3306);                                // true
var_dump($config['mode'] === 'production');              // true
```

## Injection d'objets

### Définition d'une classe

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserService
{
    private string $name;
    
    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
}
```

### Enregistrement d'un service

Il existe plusieurs façons d'enregistrer une classe dans le conteneur :

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\UserService;

$container = Container::getInstance();

// Méthode 1 : En utilisant une clé personnalisée
$container->bind('user.service', UserService::class);

// Méthode 2 : En utilisant le nom complet de la classe
$container->bind('App\Services\UserService');

// Méthode 3 : En utilisant la constante de classe (recommandé)
$container->bind(UserService::class);
```

### Récupération d'un service

```php
// Récupération par clé personnalisée
$service1 = $container->get('user.service');

// Récupération par nom de classe complet
$service2 = $container->get('App\Services\UserService');

// Récupération par constante de classe (recommandé)
$service3 = $container->get(UserService::class);

// Par défaut, une nouvelle instance est créée à chaque appel
var_dump($service1 === $service2); // false
var_dump($service2 === $service3); // false
```

## Bonnes pratiques

1. Utilisez toujours `declare(strict_types=1)` en haut de vos fichiers
2. Préférez l'utilisation des constantes de classe (`UserService::class`) plutôt que les chaînes de caractères
3. Organisez vos services dans des namespaces logiques
4. Utilisez des interfaces pour définir les contrats de vos services

## Prochaines étapes

- [Injection par constructeur](constructor_injection.md) - Apprenez à injecter des dépendances via le constructeur
- [Injection par méthodes](methodes_injection.md) - Découvrez comment configurer l'injection par méthodes
- [Autowiring](autowiring.md) - Utilisez l'autowiring pour une configuration automatique
- [Configuration](array_ou_fichier_de_configuration.md) - Configurez vos services via des tableaux ou des fichiers