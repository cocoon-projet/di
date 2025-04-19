# Injection d'Interface

L'injection d'interface est un mécanisme puissant qui permet de découpler les dépendances concrètes en utilisant des abstractions. Cela favorise la flexibilité et la testabilité du code.

## Principe de base

1. Définir une interface qui représente le contrat
2. Implémenter l'interface dans une ou plusieurs classes concrètes
3. Injecter l'interface dans les classes qui en ont besoin

## Exemple complet

### 1. Définition de l'interface

```php
<?php
declare(strict_types=1);

namespace App\Services;

interface LoggerInterface
{
    public function log(string $message, string $level = 'info'): void;
    public function error(string $message): void;
    public function debug(string $message): void;
}
```

### 2. Implémentation concrète

```php
<?php
declare(strict_types=1);

namespace App\Services;

class FileLogger implements LoggerInterface
{
    private string $logFile;
    
    public function __construct(string $logFile = 'app.log')
    {
        $this->logFile = $logFile;
    }
    
    public function log(string $message, string $level = 'info'): void
    {
        $logMessage = sprintf(
            "[%s] %s: %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    public function error(string $message): void
    {
        $this->log($message, 'error');
    }
    
    public function debug(string $message): void
    {
        $this->log($message, 'debug');
    }
}
```

### 3. Classe utilisant l'interface

```php
<?php
declare(strict_types=1);

namespace App\Services;

class UserService
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public function createUser(string $username): void
    {
        try {
            // Logique de création d'utilisateur
            $this->logger->debug("Tentative de création de l'utilisateur: $username");
            // ...
            $this->logger->log("Utilisateur créé avec succès: $username");
        } catch (\Exception $e) {
            $this->logger->error("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            throw $e;
        }
    }
}
```

## Utilisation avec le conteneur

### Méthode 1 : Liaison explicite

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\LoggerInterface;
use App\Services\FileLogger;
use App\Services\UserService;

$di = Container::getInstance();

// Enregistrement de l'implémentation pour l'interface
$di->bind(LoggerInterface::class, FileLogger::class);

// Enregistrement du service utilisant l'interface
$di->bind(UserService::class, [
    '@constructor' => [LoggerInterface::class]
]);

// Utilisation
$userService = $di->get(UserService::class);
$userService->createUser('john_doe');
```

### Méthode 2 : Liaison implicite

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\FileLogger;
use App\Services\UserService;

$di = Container::getInstance();

// Enregistrement direct de l'implémentation
$di->bind(FileLogger::class);

// Le conteneur résoudra automatiquement la dépendance
$di->bind(UserService::class, [
    '@constructor' => [FileLogger::class]
]);

// Utilisation
$userService = $di->get(UserService::class);
```

## Avantages de l'injection d'interface

1. **Découplage** : Les classes dépendent d'abstractions, pas d'implémentations
2. **Testabilité** : Facilite la création de mocks pour les tests
3. **Flexibilité** : Permet de changer facilement d'implémentation
4. **Maintenabilité** : Meilleure organisation du code

## Bonnes pratiques

1. **Nommage des interfaces** :
   - Utilisez le suffixe `Interface` pour les interfaces
   - Les noms doivent refléter le comportement, pas l'implémentation

2. **Définition des interfaces** :
   - Gardez les interfaces petites et cohérentes
   - Définissez clairement le contrat
   - Évitez les dépendances vers des implémentations concrètes

3. **Enregistrement dans le conteneur** :
   - Privilégiez la liaison explicite pour plus de clarté
   - Documentez les liaisons dans la configuration
   - Utilisez des alias significatifs

## Exemple avancé avec plusieurs implémentations

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;
use App\Services\LoggerInterface;
use App\Services\FileLogger;
use App\Services\DatabaseLogger;
use App\Services\UserService;

$di = Container::getInstance();

// Configuration basée sur l'environnement
if ($_ENV['APP_ENV'] === 'production') {
    $di->bind(LoggerInterface::class, DatabaseLogger::class);
} else {
    $di->bind(LoggerInterface::class, FileLogger::class);
}

// Le reste du code reste inchangé
$di->bind(UserService::class, [
    '@constructor' => [LoggerInterface::class]
]);
```