<?php
declare(strict_types=1);

namespace Cocoon\Dependency;

use Cocoon\Dependency\Features\AttributesManagerContainerTrait;
use Cocoon\Dependency\Features\AutowireContainerTrait;
use Cocoon\Dependency\Features\CompilerContainerTrait;
use Cocoon\Dependency\Features\ProxyManagerContainerTrait;
use Cocoon\Dependency\Features\ResolverContainerTrait;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;

/**
 * PSR-11 Container Implementation
 */
class Container implements ContainerInterface
{
    use AttributesManagerContainerTrait;
    use AutowireContainerTrait;
    use CompilerContainerTrait;
    use ProxyManagerContainerTrait;
    use ResolverContainerTrait;

    /** @var array<string, mixed> */
    protected array $services = [];

    /** @var array<string, object> */
    protected array $singleton = [];

    private static ?self $instance = null;

    private function __construct()
    {
        $this->cacheEnabled = false;
    }

    private function __clone()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param array<string, mixed>|string $services
     * @throws ContainerException
     */
    public function addServices(array|string $services): self
    {
        if (is_string($services)) {
            if (!file_exists($services)) {
                throw new ContainerException(
                    sprintf('Le fichier %s n\'existe pas', $services)
                );
            }
            $services = require $services;
            
            if (!is_array($services)) {
                throw new ContainerException('Le fichier doit retourner un tableau');
            }
        }

        foreach ($services as $alias => $service) {
            $this->bind($alias, $service);
        }
        
        return self::getInstance();
    }

    /**
     * @param string|array|bool|null|int|float|object|callable <string, mixed> $service
     * @throws ContainerException
     */
    public function bind(string $alias, string|array|bool|null|int|float|object|callable $service = '@alias'): self
    {
        if (!is_string($alias)) {
            throw new ContainerException('L\'alias doit être une chaîne de caractères');
        }

        if (is_object($service) && get_class($service) === \stdClass::class) {
            throw new ContainerException('Les objets stdClass ne sont pas autorisés comme service');
        }

        $this->services[trim($alias)] = $service === '@alias' ? trim($alias) : $service;
        
        return self::getInstance();
    }

    /**
     * @throws NotFoundServiceException
     * @throws ReflectionException
     */
    public function get(string $alias): mixed
    {
        if ($this->cacheEnabled && $this->isCompiled()) {
            return $this->getCompiled($alias);
        }

        $alias = trim($alias);
        if (!$this->has($alias)) {
            throw new NotFoundServiceException(
                sprintf('Le service "%s" n\'est pas enregistré', $alias)
            );
        }
        
        return $this->resolveService($alias);
    }

    /**
     * @param array{0: string, 1?: string} $callable
     * @param array<mixed> $vars
     * @throws ReflectionException
     */
    protected function call(array $callable, array $vars = []): mixed
    {
        if (isset($callable[1])) {
            $class = new ReflectionClass($callable[0]);
            $method = $class->getMethod($callable[1]);
            
            if ($class->isInstantiable() && !$method->isStatic()) {
                $instance = $this->has($callable[0]) 
                    ? $this->get($callable[0]) 
                    : new $callable[0]();
                    
                return $instance->{$callable[1]}(...$vars);
            }
            
            return $callable[0]::{$callable[1]}(...$vars);
        }

        $instance = new $callable[0]();
        return $instance(...$vars);
    }

    /**
     * @param array{0: string, 1?: string} $callable
     * @param array<mixed> $vars
     * @throws ContainerException
     */
    public function factory(string $alias, array $callable = [], array $vars = []): self
    {
        if (!isset($callable[0]) || !class_exists($callable[0])) {
            throw new ContainerException('Le callable doit être un tableau contenant une classe valide');
        }

        if (isset($callable[1]) && !method_exists($callable[0], $callable[1])) {
            throw new ContainerException(
                sprintf('La méthode %s n\'existe pas dans la classe %s', $callable[1], $callable[0])
            );
        }

        return $this->bind($alias, [
            '@factory' => $callable,
            '@arguments' => $vars
        ]);
    }

    public function singleton(string $alias, string|null $service = null): self
    {
        $class = $service ?? $alias;
        return $this->bind($alias, [
            '@class' => $class,
            '@singleton' => true
        ]);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function lazy(string $class, array $params = []): self
    {
        $config = ['@lazy' => true];
        $config = ['@class' => $class];
        if ($params !== []) {
            $config['@constructor'] = $params;
        }
        
        return $this->bind($class, $config);
    }

    protected function isSingleton(string $alias): bool
    {
        return isset($this->singleton[$alias]);
    }

    public function has(string $alias): bool
    {
        return isset($this->services[$alias]);
    }

    protected function hasOption(string $alias, string $key): bool
    {
        return isset($this->services[$alias][$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getServices(): array
    {
        return $this->services;
    }

    protected function putInCache(string $alias, object $service): void
    {
        $this->singleton[$alias] = $service;
    }

    protected function getFromCache(string $alias): object
    {
        return $this->singleton[$alias];
    }

    /**
     * Réinitialise le container en vidant les services et les singletons
     */
    public function reset(): self
    {
        $this->services = [];
        $this->singleton = [];
        $this->compiled = false;
        $this->compiledClass = null;
        return $this;
    }

    /**
     * Enregistre un service avec autowiring
     *
     * @param string $alias
     * @param string $class
     * @param string|null $method
     * @param array $params
     * @return self
     */
    public function autowire(string $class, ?string $method = null, array $params = [])
    {
        return $this->make($class, $method, $params);
    }

    public function inject(string $alias,array $params = []): self
    {
        $config = ['@inject' => true];
        $config = ['@class' => $alias];
        if($params !== []) {
            $config['@constructor'] = $params;
        }
        $this->bind($alias, $config);
        return $this;
    }

    /**
     * Configure les dépendances d'un service
     *
     * @param array<array<string, string|object>> $dependencies
     * @return self
     * @throws ContainerException
     */
    public function with(array $dependencies): self
    {
        foreach ($dependencies as $dependency) {
            if (!is_array($dependency)) {
                throw new ContainerException('Les dépendances doivent être des tableaux');
            }

            foreach ($dependency as $interface => $implementation) {
                if (!is_string($interface)) {
                    throw new ContainerException('L\'interface doit être une chaîne de caractères');
                }

                $this->bind($interface, $implementation);
            }
        }

        return $this;
    }


}
