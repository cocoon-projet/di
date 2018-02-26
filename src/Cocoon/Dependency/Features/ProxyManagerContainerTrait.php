<?php
namespace Cocoon\Dependency\Features;

use Cocoon\Dependency\ContainerException;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;

/**
 * Gestion lazy injection
 *
 * Trait ProxyManagerContainerTrait
 * @package Cocoon\Dependency\Features
 */
trait ProxyManagerContainerTrait
{
    /**
     * Création d'un Objet Proxy. Le Proxy instanciera l'objet
     * d'origine uniquement si nécessaire.
     *
     * @see https://ocramius.github.io/ProxyManager/docs/lazy-loading-value-holder.html
     * @see https://github.com/Ocramius/ProxyManager
     * @param $alias une classe php  ex: ClassName::class
     * @return \ProxyManager\Proxy\VirtualProxyInterface
     */
    protected function createProxy($alias)
    {
        if (!class_exists($alias)) {
            throw new ContainerException('l\'alias doit être une classe ex: ClassName::class');
        }
        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($alias) {
            $initializer   = null; // disable initialization
            $wrappedObject = $this->makeInstance($alias);
            ; // fill your object with values here

            return true; // confirm that initialization occurred correctly
        };
        return $factory->createProxy($alias, $initializer);
    }

    /**
     * Verifie si la classe (object service) doit être lazy loader
     *
     * @param $alias un classe php
     * @return bool
     */
    protected function isLazy($alias) :bool
    {
        return isset($this->services[$alias]['@lazy']);
    }
}
