<?php
namespace Cocoon\Dependency\Features;

use Cocoon\Dependency\ContainerException;
use ReflectionClass;

/**
 * Résolution des dépendances
 *
 * Trait ResolverContainerTrait
 * @package Dependency\Features
 */
trait ResolverContainerTrait
{
    /**
     * Résolution de la définition d'un service
     *
     * @param  string $alias Alias du service
     * @return string|callable|object Retourne le service
     * @throws \ReflectionException
     */
    protected function resolveService($alias)
    {
        if ($this->isSingleton($alias)) {
            return $this->getFromCache($alias);
        }

        if (is_callable($this->services[$alias])) {
            return $this->services[$alias]();
        }

        if (is_array($this->services[$alias])) {
            if (isset($this->services[$alias]['@factory'])) {
                $vars = $this->services[$alias]['@arguments'] ?? [];
                return $this->call($this->services[$alias]['@factory'], $vars);
            }

            if (class_exists($alias) && !isset($this->services[$alias]['@class'])) {
                $this->services[$alias]['@class'] = $alias;
            } elseif (!class_exists($alias) && !isset($this->services[$alias]['@class'])) {
                return $this->services[$alias];
            }
            //------- lazy injection -----------
            if ($this->isLazy($alias)) {
                return $this->createProxy($alias);
            }
            //-----------------------
            $instance = $this->makeInstance($alias);

            if ($this->hasOption($alias, '@singleton') && !$this->isSingleton($alias)) {
                $this->putInCache($alias, $instance);
            }
            if ($this->hasOption($alias, '@methods')) {
                foreach ($this->services[$alias]['@methods'] as $method => $parameters) {
                    $instance->$method(...$this->resolveArguments($parameters));
                }
            }

            return $instance;
        }
        if (is_string($this->services[$alias]) && class_exists($this->services[$alias])) {
            $class = new ReflectionClass($this->services[$alias]);
            if ($class->isInstantiable()) {
                return new $this->services[$alias]();
            } elseif ($class->hasMethod('getInstance')) {
                $instance = $this->services[$alias]::getInstance();
                $this->putInCache($alias, $instance);
                return $instance;
            } else {
                throw new ContainerException('La définition du service ne peut être définit');
            }
        } else {
            return $this->services[$alias];
        }
    }

    /**
     * Résolution du type d'argument dans le constructeur ou les méthodes
     *
     * @param  string $parameters Paramètres du constructeur et des méthodes
     * @return array   Retourne les arguments définient
     * @throws \ReflectionException
     */
    protected function resolveArguments($parameters = []) :array
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
    protected function makeInstance($alias)
    {
        $class = $this->services[$alias]['@class'];

        if ($this->hasOption($alias, '@constructor')) {
            return new $class(...$this->resolveArguments($this->services[$alias]['@constructor']));
        } else {
            return new $class();
        }
    }
}
