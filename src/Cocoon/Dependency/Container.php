<?php
namespace Cocoon\Dependency;

use Cocoon\Dependency\Features\ProxyManagerContainerTrait;
use Cocoon\Dependency\Features\ResolverContainerTrait;
use Interop\Container\ContainerInterface;
use Cocoon\Dependency\Features\AutowireContainerTrait;
use ReflectionClass;

/**
 * Class Container
 * @package Dependency
 */
class Container implements ContainerInterface
{
    /**
     * liste des services enregistrés
     *
     * @var array
     */
    protected $services = [];
    /**
     * Liste des services singleton
     *
     * @var array
     */
    protected $singleton = [];
    /**
     * Instance du container
     *
     * @var null
     */
    private static $instance = null;

    use AutowireContainerTrait, ResolverContainerTrait, ProxyManagerContainerTrait;
    /**
     * Container constructor. privé
     */
    private function __construct()
    {
    }

    /**
     * Clonage interdit
     */
    private function __clone()
    {
    }

    /**
     * Initialise le container
     *
     * @return Container|null instance de Container::class
     */
    public static function getInstance() :self
    {
        if (is_null(self::$instance)) {
            self::$instance = new Container();
        }
        return self::$instance;
    }

    /**
     * Ajoute les services à partir d'un tableau php ou un fichier
     *
     * @param array $services ou nom d'un fichier
     * @return self
     */
    public function addServices($services = null) :self
    {

        if (is_string($services)) {
            if (file_exists($services)) {
                $services = require $services;
            } else {
                throw new ContainerException('le fichier ' . $services . ' n\'existe pas');
            }
        }
        if (!is_array($services)) {
            throw new ContainerException('le paramètre services doivent être de type array');
        }
        foreach ($services as $alias => $service) {
            $this->bind($alias, $service);
        }
        return self::$instance;
    }

    /**
     * Enregistre un service
     *
     * @param string $alias alias du service
     * @param string|array $service
     * Clefs réservées array(@class, @singleton, @constructor, @methods, @factory, @arguments, @lazy)
     * définition du service
     * @return self
     */
    public function bind($alias, $service = null) :self
    {
        if (is_numeric($alias) or !is_string($alias)) {
            throw new ContainerException('l\'alias ou le service doivent être de type string');
        }
        if ($service === null) {
            $this->services[trim($alias)] = trim($alias);
        } else {
            $this->services[trim($alias)] = $service;
        }
        return self::$instance;
    }

    /**
     * Retourne le service selon l'alias definit
     *
     * @param  string $alias Nom de l'alias
     * @return string|object  Retourne le service
     * @throws \ReflectionException
     */
    public function get($alias)
    {
        if (!$this->has(trim($alias))) {
            throw new NotFoundServiceException('Ce service:  "'. $alias . '" n\est pas enregistré');
        }
        return $this->resolveService($alias);
    }

    /**
     * Résolution des dépendances type factory injection
     *
     * @param array $callable
     * @param array $vars
     * @return mixed
     * @throws \ReflectionException
     */
    protected function call($callable = [], $vars = [])
    {
        if (isset($callable[1])) {
            $class = new ReflectionClass($callable[0]);
            if ($class->isInstantiable() && !$class->getMethod($callable[1])->isStatic()) {
                $handler = ($this->has($callable[0])) ? [$this->get($callable[0]), $callable[1]]
                    : [new $callable[0], $callable[1]];
            } else {
                $handler = [$callable[0], $callable[1]];
            }
        }
        if (!isset($callable[1])) {
            $handler = new $callable[0]();
        }

        return call_user_func_array($handler, $vars);
    }

    /**
     * initialise un service a partir d'une classe et sa méthode
     * méthode, méthode static ou une classe avec la méthode __invoke()
     *
     * @param string $alias
     * @param array $callable
     * @param array $vars
     * @return self
     */
    public function factory($alias, $callable = [], $vars = []) :self
    {
        $this->bind($alias, ['@factory' => $callable,
                             '@arguments' => $vars]);
        return self::$instance;
    }

    /**
     * Initialise un service en tant que singleton
     *
     * @param string $alias
     * @param null|object $service
     * @return Container
     */
    public function singleton($alias, $service = null) :self
    {
        $class = $service ?? $alias;
        return $this->bind($alias, ['@class' => $class,
                             '@singleton' => true]);
    }

    /**
     * initialise un service Lazy Injection
     *
     * @param object $class ex: ClassName::class
     * @param array $params arguments du contructeur
     * @return Container
     */
    public function lazy($class, $params = []) :self
    {
        if (count($params) > 0) {
            return $this->bind($class, ['@lazy' => true, '@constructor' => $params]);
        } else {
            return $this->bind($class, ['@lazy' => true ]);
        }
    }

    /**
     * Verifie si le service est un singleton
     *
     * @param  string  $alias alias du service
     * @return boolean  vrai si le service est un singleton
     */
    protected function isSingleton($alias) :bool
    {
        return isset($this->singleton[$alias]);
    }
    /**
     * Vérifie si un service existe
     *
     * @param  string  $alias alias du service
     * @return boolean     vrai si retourne true
     */
    public function has($alias) :bool
    {
        return isset($this->services[$alias]);
    }
    /**
     * Verifie si l' option du service existe
     *
     * @param  string  $key option définit
     * @return boolean  retrourne vrai si l'option est identifié
     */
    protected function hasOption($alias, $key) :bool
    {
        return isset($this->services[$alias][$key]);
    }
   /**
    * Retourne la liste des services enregistrés
    *
    * @return array
    */
    public function getServices() :array
    {
        return $this->services;
    }
    /**
     * Met un service en cache (singleton)
     *
     * @param  string $alias    alias du service
     * @param  object $services nom de la classe
     * @return void
     */
    protected function putInCache($alias, $services)
    {
        $this->singleton[$alias] = $services;
    }
    /**
     * Retourne un service si il est en cahe (type singleton)
     *
     * @param  string $alias alias du service
     * @return object
     */
    protected function getFromCache($alias)
    {
        return $this->singleton[$alias];
    }
}
