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
    public function createProxy($alias)
    {
        if (!class_exists($alias)) {
            throw new ContainerException('l\'alias doit être une classe ex: ClassName::class');
        }
        $factory = new LazyLoadingValueHolderFactory();
        $initializer = function (& $wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, & $initializer) use ($alias) {
            $initializer   = null; // disable initialization
            $wrappedObject = $this->makeInstance($alias); // fill your object with values here

            return true; // confirm that initialization occurred correctly
        };
        return $factory->createProxy($alias, $initializer);
    }

    protected function isLazy($alias)
    {
        return isset($this->services[$alias]['@lazy']);
    }
}