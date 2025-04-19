<?php
declare(strict_types=1);

namespace Cocoon\Dependency;

/**
 * Façade pour l'accès statique au container
 */
class DI
{
    /**
     * Délègue les appels statiques au container
     *
     * @param string $name
     * @param array<mixed> $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return Container::getInstance()->$name(...$arguments);
    }
}
