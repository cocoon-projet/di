<?php
namespace Cocoon\Dependency\Features;

use ReflectionClass;

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
        } else {
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
    }

    /**
     * Résolution des paramètres d'injection du constructeur et des methodes
     *
     * @param array $parameters
     * @param array $vars
     * @return array
     * @throws \ReflectionException
     */
    protected function resolveInjection($parameters = [], $vars = []) :array
    {
        $params = [];
        foreach ($parameters as $param) {
            if (!is_null($param->getClass())) {
                $class_name = $param->getClass()->getName();
                $params[] = ($this->has($class_name)) ? $this->get($class_name) : $this->make($class_name);
            } else {
                if (isset($vars[$param->getName()])) {
                    $params[] = $vars[$param->getName()];
                } else {
                    $params[] = ($param->isDefaultValueAvailable() == false) ? null : $param->getDefaultValue();
                }
            }
        }
        return $params;
    }
}
