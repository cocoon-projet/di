<?php
namespace Cocoon\Dependency\Features;

use ReflectionClass;
use ReflectionParameter;
use ReflectionNamedType;
use ReflectionUnionType;

/**
 * Autowiring implémentation
 *
 * Trait AutowireContainerTrait
 * @package Dependency\Features
 */
trait AutowireContainerTrait
{
    /**
     *  Autowiring
     *
     * @param string $class
     * @param null|array|string $mixed   string = methode de classe : array = arguments du constructeur
     * @param array $vars arguments de la méthode indiquée dans $mixed
     * @return object
     * @throws \ReflectionException
     */
    public function make($class, $mixed = null, $vars = [])
    {
        $method = null;
        $constructorArguments = [];
        if (is_string($mixed)) {
            $method = $mixed;
        } elseif (is_array($mixed)) {
            $constructorArguments = $mixed;
        }
        $reflexion = new ReflectionClass($class);
        $constructor = $reflexion->getConstructor();
        if (is_null($constructor) && $method == null) {
            return new $class();
        }
        // autowiring constructor
        if (!is_null($constructor)) {
            $constructor_params = $constructor->getParameters();
            if (count($constructor_params) == 0 && $method == null) {
                return new $class();
            } else {
                // initialisation des paramètres du constructeur
                $args = $this->resolveInjection($constructor_params, $constructorArguments);
            }
        }

        if ($method == null) {
            return new $class(...$args);
        }
        if (is_null($constructor) or count($constructor_params) == 0) {
            $instance = new $class();
        } else {
            $instance = new $class(...$args);
        }
        // autowiring method
        if ($method != null) {
            $params = $reflexion->getMethod($method)->getParameters();
            if (count($params) == 0) {
                return $instance->$method();
            } else {
                // initialisation des paramètres de la méthode
                return $instance->$method(...$this->resolveInjection($params, $vars));
            }
        }
        
        return $instance;
    }

    /**
     * Résolution des paramètres d'injection du constructeur et des methodes
     *
     * @param array $parameters
     * @param array $vars
     * @return array
     * @throws \ReflectionException
     */
    protected function resolveInjection(array $parameters, array $vars = []): array
    {
        return array_map(
            function (ReflectionParameter $param) use ($vars) {
                $type = $param->getType();
                
                // Gestion des types union de PHP 8
                if ($type instanceof ReflectionUnionType) {
                    foreach ($type->getTypes() as $unionType) {
                        if (!$unionType->isBuiltin()) {
                            $className = $unionType->getName();
                            return $this->resolveClassName($className);
                        }
                    }
                }
                
                // Gestion des types simples et null
                if ($type === null || ($type instanceof ReflectionNamedType && !$type->isBuiltin())) {
                    $className = $type === null ? null : $type->getName();
                    if ($className !== null) {
                        return $this->resolveClassName($className);
                    }
                }
                
                // Gestion des paramètres nommés
                $paramName = $param->getName();
                if (isset($vars[$paramName])) {
                    return $vars[$paramName];
                }
                
                // Valeur par défaut ou null
                return $param->isDefaultValueAvailable() 
                    ? $param->getDefaultValue() 
                    : null;
            },
            $parameters
        );
    }
    /**
     * Résout une classe par son nom
     *
     * @template T of object
     * @param class-string<T> $className
     * @return T
     * @throws \ReflectionException
     */
    private function resolveClassName(string $className): object
    {
        return $this->has($className) 
            ? $this->get($className) 
            : $this->make($className);
    }
}