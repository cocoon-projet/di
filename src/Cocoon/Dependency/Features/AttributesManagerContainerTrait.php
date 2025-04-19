<?php
declare(strict_types=1);

namespace Cocoon\Dependency\Features;

use Cocoon\Dependency\ContainerException;
use Cocoon\Dependency\Features\Attributes\Inject;
use ReflectionClass;
use ReflectionProperty;
use ReflectionParameter;

/**
 * Gestion de l'injection de dépendances via les attributs PHP 8
 */
trait AttributesManagerContainerTrait
{
    /**
     * Injecte les dépendances dans une instance via les attributs
     *
     * @template T of object
     * @param class-string<T>|T $class
     * @return T
     * @throws ContainerException
     */
    public function injectAttributes(string|object $class): object
    {
        $reflection = new ReflectionClass($class);
        $instance = is_string($class) ? $this->createInstance($reflection) : $class;

        // Injection des propriétés
        foreach ($reflection->getProperties() as $property) {
            $attributes = $property->getAttributes(Inject::class);
            
            if (count($attributes) === 0) {
                continue;
            }

            $inject = $attributes[0]->newInstance();
            $service = $inject->getService() ?? $property->getType()?->getName();
            
            if ($service === null) {
                throw new ContainerException(
                    sprintf('Le type de la propriété %s n\'est pas défini', $property->getName())
                );
            }

            if (!$this->has($service)) {
                throw new ContainerException(
                    sprintf('Le service %s n\'est pas défini', $service)
                );
            }

            $property->setAccessible(true);
            $property->setValue($instance, $this->get($service));
        }

        return $instance;
    }

    /**
     * Crée une instance en gérant l'injection des paramètres du constructeur
     *
     * @throws ContainerException
     */
    private function createInstance(ReflectionClass $reflection): object
    {
        $constructor = $reflection->getConstructor();
        
        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $parameters = $constructor->getParameters();
        $args = [];

        foreach ($parameters as $param) {
            $attributes = $param->getAttributes(Inject::class);
            
            if (count($attributes) === 0) {
                if ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    throw new ContainerException(
                        sprintf('Le paramètre %s du constructeur n\'a pas de valeur par défaut', $param->getName())
                    );
                }
                continue;
            }

            $inject = $attributes[0]->newInstance();
            $service = $inject->getService() ?? $param->getType()?->getName();
            
            if ($service === null) {
                throw new ContainerException(
                    sprintf('Le type du paramètre %s n\'est pas défini', $param->getName())
                );
            }

            if (!$this->has($service)) {
                throw new ContainerException(
                    sprintf('Le service %s n\'est pas défini', $service)
                );
            }

            $args[] = $this->get($service);
        }

        return $reflection->newInstance(...$args);
    }

    /**
     * Résout les paramètres d'une méthode avec injection par attributs
     *
     * @param array<ReflectionParameter> $parameters
     * @return array<mixed>
     * @throws ContainerException
     */
    protected function resolveAttributesParameters(array $parameters): array
    {
        $args = [];
        foreach ($parameters as $param) {
            $attributes = $param->getAttributes(Inject::class);
            
            if (count($attributes) === 0) {
                $args[] = $param->isDefaultValueAvailable() 
                    ? $param->getDefaultValue() 
                    : null;
                continue;
            }

            $inject = $attributes[0]->newInstance();
            $service = $inject->getService() ?? $param->getType()?->getName();
            
            if ($service === null) {
                throw new ContainerException(
                    sprintf('Le type du paramètre %s n\'est pas défini', $param->getName())
                );
            }

            if (!$this->has($service)) {
                throw new ContainerException(
                    sprintf('Le service %s n\'est pas défini', $service)
                );
            }

            $args[] = $this->get($service);
        }

        return $args;
    }

    public function isInject(string $alias): bool
    {
        return isset($this->services[$alias]['@inject']);
    }
} 