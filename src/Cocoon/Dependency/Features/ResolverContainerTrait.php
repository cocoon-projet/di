<?php
declare(strict_types=1);

namespace Cocoon\Dependency\Features;

use Cocoon\Dependency\ContainerException;
use ReflectionClass;
use ReflectionException;

/**
 * Résolution des dépendances avec support PHP 8.x
 */
trait ResolverContainerTrait
{
    /**
     * Résout la définition d'un service
     *
     * @throws ReflectionException|ContainerException
     */
    public function resolveService(string $alias): mixed
    {
        // Gestion des singletons
        if ($this->isSingleton($alias)) {
            return $this->getFromCache($alias);
        }

        $service = $this->services[$alias];

        // Gestion des callables
        if (is_callable($service)) {
            return $service();
        }

        // Gestion des configurations array
        if (is_array($service)) {
            return $this->resolveArrayService($alias, $service);
        }

        // Gestion des services string
        if (is_string($service) && class_exists($service)) {
            return $this->resolveStringService($service, $alias);
        }

        return $service;
    }

    /**
     * Résout un service défini comme tableau
     *
     * @param array<string, mixed> $service
     * @throws ReflectionException|ContainerException
     */
    public function resolveArrayService(string $alias, array $service): mixed
    {
        // Gestion des factories
        if (isset($service['@factory'])) {
            return $this->call(
                $service['@factory'],
                $service['@arguments'] ?? []
            );
        }

        // Gestion des classes
        if (class_exists($alias) && !isset($service['@class'])) {
            $service['@class'] = $alias;
        } elseif (!class_exists($alias) && !isset($service['@class']) && !$this->isLazy($alias)) {
            return $service;
        }
        // gestion dependance par attribut
        if ($this->isInject($alias)) {
            return $this->injectAttributes($alias);
        }

        // Gestion du lazy loading
        if ($this->isLazy($alias)) {
            return $this->createProxy($alias);
        }

        $instance = $this->makeInstance($alias);

        // Gestion des singletons
        if ($this->hasOption($alias, '@singleton') && !$this->isSingleton($alias)) {
            $this->putInCache($alias, $instance);
        }

        // Gestion des appels de méthodes
        if ($this->hasOption($alias, '@methods')) {
            $this->invokeMethods($instance, $service['@methods']);
        }

        return $instance;
    }

    /**
     * Résout un service défini comme string
     *
     * @throws ReflectionException|ContainerException
     */
    public function resolveStringService(string $service, string $alias): object
    {
        $class = new ReflectionClass($service);

        if ($class->isInstantiable()) {
            return new $service();
        }

        if ($class->hasMethod('getInstance')) {
            $instance = $service::getInstance();
            $this->putInCache($alias, $instance);
            return $instance;
        }

        throw new ContainerException(
            sprintf('Le service %s ne peut pas être instancié', $service)
        );
    }

    /**
     * Invoque les méthodes configurées sur une instance
     *
     * @param array<string, array<mixed>> $methods
     */
    public function invokeMethods(object $instance, array $methods): void
    {
        foreach ($methods as $method => $parameters) {
            $instance->$method(...$this->resolveArguments($parameters));
        }
    }

    /**
     * Résolution du type d'argument dans le constructeur ou les méthodes
     *
     * @param  array $parameters Paramètres du constructeur et des méthodes
     * @return array   Retourne les arguments définient
     * @throws \ReflectionException
     */
    public function resolveArguments($parameters = []): array
    {
        $args = [];
        foreach ($parameters as $key) {
            if (is_string($key) && $this->has($key)) {
                $args[] = $this->get($key);
            } else {
                $args[] = $key;
            }
        }
        return $args;
    }

    /**
     * Retourne l'instance d'une classe (service)
     *
     * @param string $alias
     * @return object instance d'une classe
     * @throws \ReflectionException
     */
    public function makeInstance($alias)
    {
        $class = $this->services[$alias]['@class'];

        if ($this->hasOption($alias, '@constructor')) {
            return new $class(...$this->resolveArguments($this->services[$alias]['@constructor']));
        } else {
            return new $class();
        }
    }

    /**
     * Vérifie si un service est un singleton
     */
    public function isSingleton(string $alias): bool
    {
        return isset($this->singletons[$alias]);
    }

    /**
     * Récupère un service du cache
     */
    public function getFromCache(string $alias): object
    {
        return $this->singletons[$alias];
    }

    /**
     * Vérifie si une option existe pour un service
     */
    public function hasOption(string $alias, string $option): bool
    {
        return isset($this->services[$alias][$option]);
    }

    /**
     * Appelle une méthode avec des arguments
     */
    public function call(callable $callable, array $arguments = []): mixed
    {
        return $callable(...$this->resolveArguments($arguments));
    }

    /**
     * Met un service en cache
     */
    public function putInCache(string $alias, object $instance): void
    {
        $this->singletons[$alias] = $instance;
    }
}
