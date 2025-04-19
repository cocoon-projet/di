# Système de Cache du Conteneur

## Introduction

Le système de cache du conteneur permet d'optimiser les performances en compilant les services en une classe unique. Cette fonctionnalité est particulièrement utile dans les environnements de production où les performances sont critiques.

## Configuration

### Activation du Cache

```php
$container = new Container();
//activation du cache
$container->setCacheConfig(true,' /app/Cache');
```

#### Dossier de Cache
- Doit etre definit dans setCacheConfig(true, $dir);
- le dossier est crée automatiquement


## Utilisation

### Compilation des Services

```php
// Configuration des services
$container->bind('logger', [
    '@class' => Logger::class,
    '@singleton' => true
]);

// Compilation des services
$container->compile();
```

### Chargement des Services Compilés

Les services compilés sont automatiquement chargés lors de la première utilisation. Vous pouvez également les charger manuellement :

```php
$container->loadCompiled();
```

## Gestion des Erreurs

### Dossier de Cache Inexistant

```php
try {
    $container->compile();
} catch (ContainerException $e) {
    // Le dossier de cache n'existe pas
    mkdir('/chemin/vers/votre/cache', 0755, true);
    $container->compile();
}
```

### Problèmes de Permissions

```php
if (!is_writable($container->getCacheDir())) {
    throw new RuntimeException('Le dossier de cache n\'est pas accessible en écriture');
}
```

### Cache 

```php

$container->compile();
```

## Bonnes Pratiques

1. **Configuration du Cache**
   - Définir le dossier de cache au début de l'application
   - Créer le dossier de cache si nécessaire

2. **Environnement de Production**
   - Toujours activer le cache
   - Compiler les services après chaque déploiement

3. **Environnement de Développement**
   - Désactiver le cache pour faciliter le débogage
   - Recompiler fréquemment pour tester les changements
   - Utiliser un dossier de cache séparé

## Exemple Complet

```php
// Configuration
$container = new Container();

//activation du cache
$container->setCacheConfig(true,'/app/Cache');

// Définition des services
$container->bind('logger', [
    '@class' => Logger::class,
    '@singleton' => true
]);

$container->bind('db', [
    '@class' => Database::class,
    '@arguments' => ['host' => 'localhost'],
    '@lazy' => true
]);

// Compilation
$container->compile();

// Utilisation
$logger = $container->get('logger');
$db = $container->get('db');
```

## Limitations

1. **Services Dynamiques**
   - Les services créés dynamiquement ne sont pas mis en cache
   - Les closures ne sont pas supportées dans le cache

2. **Modifications**
   - Les modifications des services nécessitent une recompilation
   - Le cache doit être vidé lors des mises à jour

3. **Namespaces**
   - Les conflits de namespace doivent être évités

## API Référence

### Méthodes Principales

- `setCacheConfig(bool $enabled, ?string $cacheDir, ?string $basePath)`: Configure le cache
- `compile()`: Compile les services
- `loadCompiled()`: Charge les services compilés
- `getCacheDir()`: Retourne le dossier de cache

## Fonctionnalités Supportées

### Services Singleton

```php
$container->bind('service', [
    '@class' => Service::class,
    '@singleton' => true
]);
```

### Services Lazy

```php
$container->bind('service', [
    '@class' => Service::class,
    '@lazy' => true
]);
```

### Services avec Factory

```php
$container->bind('service', [
    '@factory' => [ServiceFactory::class, 'create'],
    '@arguments' => ['param' => 'value']
]);
```

### Injection de Dépendances

```php
$container->bind('service', [
    '@class' => Service::class,
    '@inject' => true
]);
```

## Structure du Cache

Le cache génère une classe `CompiledServices` qui contient :

- Une méthode statique pour chaque service
- Un tableau de singletons
- La logique de création des services

## Exemple Complet

```php
// Configuration
$container = new Container();
$container->enableCache();

// Définition des services
$container->bind('logger', [
    '@class' => Logger::class,
    '@singleton' => true
]);

$container->bind('db', [
    '@class' => Database::class,
    '@arguments' => ['host' => 'localhost'],
    '@lazy' => true
]);

// Compilation
$container->compile();

// Utilisation
$logger = $container->get('logger');
$db = $container->get('db');
```

## Bonnes Pratiques

1. **Environnement de Production**
   - Toujours activer le cache
   - Compiler les services après chaque déploiement

2. **Environnement de Développement**
   - Désactiver le cache pour faciliter le débogage
   - Recompiler fréquemment pour tester les changements

3. **Gestion des Erreurs**
   - Vérifier les permissions du dossier de cache
   - Surveiller la taille du cache
   - Nettoyer le cache régulièrement

## Limitations

1. **Services Dynamiques**
   - Les services créés dynamiquement ne sont pas mis en cache
   - Les closures ne sont pas supportées dans le cache

2. **Modifications**
   - Les modifications des services nécessitent une recompilation
   - Le cache doit être vidé lors des mises à jour


## API Référence

### Méthodes Principales

- `setCacheConfig(true, $dir,$basePath)`: Activation du cache ($basepath optionnel)
- `compile()`: Compile les services
- `loadCompiled()`: Charge les services compilés
- `getCacheDir()`: Retourne le dossier de cache

## Exemples Avancés

### Configuration avec Arguments

```php
$container->bind('service', [
    '@class' => Service::class,
    '@arguments' => [
        'param1' => 'value1',
        'param2' => true,
        'dependency' => '@other.service'
    ],
    '@singleton' => true
]);
```

### Service avec Factory et Arguments

```php
$container->bind('service', [
    '@factory' => [ServiceFactory::class, 'create'],
    '@arguments' => [
        'config' => [
            'option1' => true,
            'option2' => '@logger'
        ]
    ]
]);
```

### Injection par attribut

```php
$container->bind('service', [
    '@class' => Service::class,
    '@inject' => true
]);
```

## Performance

Le système de cache offre plusieurs avantages en termes de performance :

1. **Réduction des Appels**
   - Les services sont instanciés une seule fois
   - Les dépendances sont résolues au moment de la compilation

2. **Optimisation de la Mémoire**
   - Les singletons sont partagés
   - Les services lazy ne sont créés que lorsqu'ils sont nécessaires

3. **Amélioration du Temps de Réponse**
   - Pas de réflexion au runtime
   - Pas de résolution de dépendances au runtime

