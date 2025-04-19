<?php
declare(strict_types=1);

namespace Cocoon\Dependency\Compiler;

use Cocoon\Dependency\Container;
use Cocoon\Dependency\ContainerException;

/**
 * Compileur de services pour optimiser les performances
 */
class ServiceCompiler
{
    private bool $enabled;
    private string $cacheDir;
    private string $cacheFile;
    private string $basePath;

    public string $createDir;

    public function __construct(bool $enabled, ?string $cacheDir, string $basePath)
    {
        $this->enabled = $enabled;
        $this->basePath = $basePath;
        
        if ($enabled && empty($cacheDir)) {
            throw new ContainerException('Le répertoire de cache doit être spécifié');
        }
        
        $this->cacheDir = $cacheDir;
        $this->createDir = $this->basePath . '/' . $this->cacheDir;
        $this->cacheFile = $this->basePath . '/' . $this->cacheDir . '/CompiledServices.php';
    }

    /**
     * Compile les services en une classe unique
     */
    public function compile(Container $container): void
    {
        if (!$this->enabled) {
            return;
        }

        $services = $container->getServices();
        $compiledCode = $this->generateCompiledClass($services);
        
        if (!is_dir($this->createDir)) {
            mkdir($this->createDir, 0755, true);
        }
        
        file_put_contents($this->cacheFile, $compiledCode);
    }

    /**
     * Retourne le chemin du fichier de cache
     */
    public function getCacheFile(): string
    {
        return $this->cacheFile;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * Génère le code de la classe compilée
     */
    private function generateCompiledClass(array $services): string
    {
        $code = "<?php\n";
        $code .= "declare(strict_types=1);\n\n";
        $code .= "class CompiledServices\n";
        $code .= "{\n";
        $code .= "    private static array \$singletons = [];\n";
        $code .= "    private static array \$services = [];\n\n";
        
        // Initialisation des services
        $code .= "    public static function initialize(array \$services): void\n";
        $code .= "    {\n";
        $code .= "        self::\$services = \$services;\n";
        $code .= "    }\n\n";
        
        // Méthode pour initialiser les services simples
        $code .= "    private static function initializeSimpleServices(): void\n";
        $code .= "    {\n";
        foreach ($services as $alias => $service) {
            if (is_callable($service)) {
                $code .= "        self::\$services['$alias'] = self::getFactory_{$this->normalizeAlias($alias)}();\n";
            } elseif (!is_array($service) || !isset($service['@class'])) {
                if (is_object($service)) {
                    $code .= "        self::\$services['$alias'] = new \\" . get_class($service) . "();\n";
                } else {
                    $code .= "        self::\$services['$alias'] = " . var_export($service, true) . ";\n";
                }
            }
        }
        $code .= "    }\n\n";
        
        // Méthode pour récupérer les services simples
        $code .= "    private static function getSimpleService(string \$alias): mixed\n";
        $code .= "    {\n";
        $code .= "        if (!isset(self::\$services[\$alias])) {\n";
        $code .= "            self::initializeSimpleServices();\n";
        $code .= "            if (!isset(self::\$services[\$alias])) {\n";
        $code .= "                throw new \\RuntimeException('Service ' . \$alias . ' not found');\n";
        $code .= "            }\n";
        $code .= "        }\n";
        $code .= "        return self::\$services[\$alias];\n";
        $code .= "    }\n\n";
        
        // Ajout des services
        foreach ($services as $alias => $service) {
            if (is_callable($service)) {
                // Pour les callables simples (comme item.factory)
                $code .= "    private static function getFactory_{$this->normalizeAlias($alias)}()\n";
                $code .= "    {\n";
                $code .= "        return new \\" . get_class($service()) . "();\n";
                $code .= "    }\n\n";
                
                // Modifier le service pour utiliser la méthode factory
                $services[$alias] = "self::getFactory_{$this->normalizeAlias($alias)}()";
            }
            $code .= $this->generateServiceCode($alias, $service);
        }
        
        $code .= "}\n";
        
        return $code;
    }

    /**
     * Génère le code pour un service
     */
    private function generateServiceCode(string $alias, mixed $service): string
    {
        $methodName = $this->normalizeAlias($alias);
        $code = "    public static function resolve{$methodName}()\n";
        $code .= "    {\n";
        
        if (is_array($service)) {
            $code .= $this->generateArrayServiceCode($alias, $service);
        } elseif (is_callable($service)) {
            $code .= "        if (isset(self::\$singletons['$alias'])) {\n";
            $code .= "            return self::\$singletons['$alias'];\n";
            $code .= "        }\n\n";
            $code .= "        \$instance = self::getFactory_{$methodName}();\n";
            $code .= "        return \$instance;\n";
        } else {
            $code .= $this->generateSimpleServiceCode($alias, $service);
        }
        
        $code .= "    }\n\n";
        
        return $code;
    }

    /**
     * Génère le code pour un service défini comme tableau
     */
    private function generateArrayServiceCode(string $alias, array $service): string
    {
        $code = "        if (isset(self::\$singletons['$alias'])) {\n";
        $code .= "            return self::\$singletons['$alias'];\n";
        $code .= "        }\n\n";
        
        if (isset($service['@class'])) {
            $class = $service['@class'];
            $args = [];
            
            // Vérifier si le service utilise l'injection par attributs
            if (isset($service['@inject']) && $service['@inject'] === true) {
                $reflection = new \ReflectionClass($class);
                
                // Gérer les dépendances du constructeur
                $constructor = $reflection->getConstructor();
                if ($constructor) {
                    $parameters = $constructor->getParameters();
                    foreach ($parameters as $parameter) {
                        $attributes = $parameter->getAttributes(\Cocoon\Dependency\Features\Attributes\Inject::class);
                        if (!empty($attributes)) {
                            $type = $parameter->getType()->getName();
                            $serviceName = $attributes[0]->getArguments()[0] ?? $type;
                            $args[] = "self::resolve" . $this->normalizeAlias($serviceName) . "()";
                        }
                    }
                }
            } elseif (isset($service['@constructor'])) {
                $reflection = new \ReflectionClass($class);
                $constructor = $reflection->getConstructor();
                
                if ($constructor) {
                    $parameters = $constructor->getParameters();
                    foreach ($parameters as $index => $parameter) {
                        if (isset($service['@constructor'][$index])) {
                            $dependency = $service['@constructor'][$index];
                            if ($parameter->getType() && !$parameter->getType()->isBuiltin()) {
                                $type = $parameter->getType()->getName();
                                $args[] = "self::resolve" . $this->normalizeAlias($type) . "()";
                            } else {
                                $args[] = var_export($dependency, true);
                            }
                        }
                    }
                }
            }
            
            $code .= "        \$instance = new \\$class(" . implode(', ', $args) . ");\n";
            
            // Gérer les propriétés avec attributs Inject après l'instanciation
            if (isset($service['@inject']) && $service['@inject'] === true) {
                $reflection = new \ReflectionClass($class);
                foreach ($reflection->getProperties() as $property) {
                    $attributes = $property->getAttributes(\Cocoon\Dependency\Features\Attributes\Inject::class);
                    if (!empty($attributes)) {
                        $type = $property->getType()->getName();
                        $serviceName = $attributes[0]->getArguments()[0] ?? $type;
                        $code .= "        \$reflection = new \\ReflectionClass('$class');\n";
                        $code .= "        \$property = \$reflection->getProperty('{$property->getName()}');\n";
                        $code .= "        \$property->setAccessible(true);\n";
                        $code .= "        \$property->setValue(\$instance, self::resolve" . $this->normalizeAlias($serviceName) . "());\n";
                    }
                }
            }
            
            // Gestion des méthodes (@methods)
            if (isset($service['@methods'])) {
                foreach ($service['@methods'] as $method => $params) {
                    $methodArgs = [];
                    foreach ($params as $param) {
                        if (is_string($param) && (class_exists($param) || interface_exists($param))) {
                            $methodArgs[] = "self::resolve" . $this->normalizeAlias($param) . "()";
                        } else {
                            $methodArgs[] = var_export($param, true);
                        }
                    }
                    $code .= "        \$instance->$method(" . implode(', ', $methodArgs) . ");\n";
                }
            }
        } elseif (isset($service['@factory'])) {
            $factory = $service['@factory'];
            $args = [];
            
            if (isset($service['@arguments'])) {
                foreach ($service['@arguments'] as $key => $value) {
                    if (is_string($value) && (class_exists($value) || interface_exists($value))) {
                        $args[] = "self::resolve" . $this->normalizeAlias($value) . "()";
                    } else {
                        $args[] = var_export($value, true);
                    }
                }
            }

            // Vérifier si c'est une méthode statique ou d'instance
            if (is_array($factory)) {
                $factoryClass = $factory[0];
                // Si on a une méthode spécifiée
                if (isset($factory[1])) {
                    $factoryMethod = $factory[1];
                    // Vérifier si la méthode est statique
                    $reflection = new \ReflectionClass($factoryClass);
                    if ($reflection->hasMethod($factoryMethod)) {
                        $method = $reflection->getMethod($factoryMethod);
                        if ($method->isStatic()) {
                            $code .= "        \$instance = \\$factoryClass::$factoryMethod(" . implode(', ', $args) . ");\n";
                        } else {
                            $code .= "        \$factory = new \\$factoryClass();\n";
                            $code .= "        \$instance = \$factory->$factoryMethod(" . implode(', ', $args) . ");\n";
                        }
                    }
                } else {
                    // Si pas de méthode spécifiée, on utilise __invoke
                    $code .= "        \$factory = new \\$factoryClass();\n";
                    $code .= "        \$instance = \$factory(" . implode(', ', $args) . ");\n";
                }
            } elseif (is_callable($factory)) {
                $code .= "        \$instance = call_user_func(" . var_export($factory, true) . ", " . implode(', ', $args) . ");\n";
            }
        } else {
            $code .= "        \$instance = self::\$services['$alias'];\n";
        }
        
        if (isset($service['@singleton'])) {
            $code .= "        self::\$singletons['$alias'] = \$instance;\n";
        }
        
        $code .= "        return \$instance;\n";
        return $code;
    }

    private function formatArguments(array $arguments): string
    {
        $formatted = [];
        foreach ($arguments as $key => $value) {
            if (is_string($value)) {
                $formatted[] = "self::resolve" . $this->normalizeAlias($value) . "()";
            } elseif (is_bool($value)) {
                $formatted[] = $value ? 'true' : 'false';
            } elseif (is_array($value)) {
                $formatted[] = '[' . $this->formatArguments($value) . ']';
            } else {
                $formatted[] = $value;
            }
        }
        return implode(', ', $formatted);
    }

    /**
     * Génère le code pour un service callable
     */
    private function generateCallableServiceCode(string $alias, callable $service): string
    {
        $code = "        return call_user_func(self::\$services['$alias']);\n";
        return $code;
    }

    /**
     * Génère le code pour un service simple
     */
    private function generateSimpleServiceCode(string $alias, mixed $service): string
    {
        $code = "        if (isset(self::\$singletons['$alias'])) {\n";
        $code .= "            return self::\$singletons['$alias'];\n";
        $code .= "        }\n\n";
        
        if (is_string($service) && strpos($service, 'self::getFactory_') === 0) {
            $code .= "        \$instance = $service;\n";
        } elseif (is_string($service) && class_exists($service)) {
            $code .= "        \$instance = new \\$service();\n";
        } else {
            // Pour les services simples, on utilise la méthode getSimpleService
            $code .= "        \$instance = self::getSimpleService('$alias');\n";
        }
        
        if (is_array($service) && isset($service['@singleton'])) {
            $code .= "        self::\$singletons['$alias'] = \$instance;\n";
        }
        
        $code .= "        return \$instance;\n";
        return $code;
    }

    /**
     * Normalise un alias pour être utilisé comme nom de méthode
     */
    private function normalizeAlias(string $alias): string
    {
        // Supprimer le namespace et ne garder que le nom de la classe
        $parts = explode('\\', $alias);
        $className = end($parts);
        
        // Remplacer les caractères spéciaux par des underscores
        $normalized = str_replace(['.', '-', ' '], '_', $className);
        
        // Convertir en camelCase
        $parts = explode('_', $normalized);
        $parts = array_map('ucfirst', $parts);
        
        return implode('', $parts);
    }
}