<?php
namespace Cocoon\Dependency;

/**
 * Facade DI class
 *
 * Class DI
 * @package Cocoon\Dependency
 */
class DI
{
    /**
     * Utilisation du container en méthodes statiques
     *
     * @param string $name méthode du container
     * @param mixed $arguments arguments des méthodes du container
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $instance = Container::getInstance();
        return $instance->$name(...$arguments);
    }
}
