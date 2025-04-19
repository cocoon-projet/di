<?php
declare(strict_types=1);

namespace Cocoon\Dependency\Features;

use Cocoon\Dependency\ContainerException;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use ProxyManager\Proxy\VirtualProxyInterface;

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
     * @see https://github.com/FriendsOfPHP/proxy-manager-lts
     * @param class-string $alias une classe php  ex: ClassName::class
     * @return VirtualProxyInterface
     * @throws ContainerException
     */
    public function createProxy(string $alias): VirtualProxyInterface
    {
        if (!class_exists(LazyLoadingValueHolderFactory::class)) {
            throw new ContainerException(
                'La bibliothèque friendsofphp/proxy-manager-lts est requise pour utiliser le lazy loading. ' .
                'Veuillez l\'installer via Composer : composer require friendsofphp/proxy-manager-lts'
            );
        }

        if (!class_exists($alias)) {
            throw new ContainerException('l\'alias doit être une classe ex: ClassName::class');
        }

        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            string $method,
            array $parameters,
            &$initializer
        ) use ($alias): bool {
            $initializer = null; // disable initialization
            $wrappedObject = $this->makeInstance($alias); // fill your object with values here

            return true; // confirm that initialization occurred correctly
        };

        return $factory->createProxy($alias, $initializer);
    }

    /**
     * Verifie si la classe (object service) doit être lazy loader
     *
     * @param class-string $alias un classe php
     * @return bool
     */
    public function isLazy(string $alias): bool
    {
        return isset($this->services[$alias]['@lazy']);
    }
}
