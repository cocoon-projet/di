# Cocoon DI - Container d'Injection de Dépendances pour PHP 8

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Build Status](https://travis-ci.org/cocoon-projet/di.svg?branch=master)](https://travis-ci.org/cocoon-projet/di)

Cocoon DI est un conteneur d'injection de dépendances moderne et puissant pour PHP 8, conçu pour être simple d'utilisation tout en offrant des fonctionnalités avancées.

## Fonctionnalités principales

- 🚀 Injection de dépendances par constructeur, méthodes et attributs
- 🔄 Support complet de l'autowiring
- 💾 Injection lazy pour optimiser les performances
- 🏭 Pattern Factory pour la création d'objets complexes
- 📦 Support des interfaces et des classes abstraites
- 🔒 Gestion des singletons
- ⚡ Configuration via tableau PHP ou fichiers de configuration
- 🎯 Support des attributs PHP 8 pour une configuration déclarative

## Installation

```bash
composer require cocoon-projet/di
```

## Démarrage rapide

```php
<?php
declare(strict_types=1);

use Cocoon\Dependency\Container;

$container = Container::getInstance();

// Enregistrement d'un service
$container->set('logger', Logger::class);

// Récupération d'un service
$logger = $container->get('logger');
```

## Documentation

### Guide d'utilisation
- [Premiers pas](premier_pas.md) - Guide de démarrage rapide
- [API](api.md) - Documentation complète de l'API
- [Injection par constructeur](constructor_injection.md) - Comment utiliser l'injection par constructeur
- [Injection par méthodes](methodes_injection.md) - Configuration des injections par méthodes
- [Injection par interfaces](interface.md) - Utilisation des interfaces pour l'injection
- [Pattern Singleton](singleton.md) - Gestion des services en singleton
- [Pattern Factory](factory.md) - Création d'objets complexes
- [Injection Lazy](lazy.md) - Optimisation des performances avec l'injection lazy
- [Autowiring](autowiring.md) - Configuration automatique des dépendances
- [Configuration](array_ou_fichier_de_configuration.md) - Configuration des services
- [Facade DI](DI.md) - Utilisation de la classe Facade

## Exemples d'utilisation

### Injection par attributs
```php
class UserService {
    #[Inject]
    private LoggerInterface $logger;

    public function __construct(
        #[Inject]
        private UserRepositoryInterface $repository
    ) {}
}
```

### Configuration via tableau
```php
$services = [
    LoggerInterface::class => Logger::class,
    UserService::class => [
        '@class' => UserService::class,
        '@constructor' => [LoggerInterface::class],
        '@methods' => [
            'setConfig' => [['debug' => true]]
        ]
    ]
];

$container->addServices($services);
```

## Contribuer

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Soumettre des pull requests

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
