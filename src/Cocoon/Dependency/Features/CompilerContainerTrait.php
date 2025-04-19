<?php
declare(strict_types=1);

namespace Cocoon\Dependency\Features;

use Cocoon\Dependency\Compiler\ServiceCompiler;
use Cocoon\Dependency\ContainerException;

/**
 * Trait pour la compilation des services
 */
trait CompilerContainerTrait
{
    private bool $compiled = false;
    private bool $cacheEnabled = false;
    private ?string $cacheDir = '';
    private ?ServiceCompiler $compiler = null;
    private string $basePath = '';

    public function setCacheConfig(bool $enabled = true, ?string $cacheDir = null, string $basePath = null): void
    {
        $this->cacheEnabled = $enabled;
        $this->basePath = $basePath ?: dirname(__DIR__, 4);
        
        if ($enabled && empty($cacheDir)) {
            throw new ContainerException('Le répertoire de cache doit être spécifié');
        }
        
        $this->cacheDir = $cacheDir;
        $this->compiler = new ServiceCompiler($enabled, $this->cacheDir, $this->basePath);
        
        if ($enabled) {
            $this->loadCompiled();
        }
    }

    /**
     * Retourne le chemin du répertoire de cache
     */
    public function getCacheDir(): string
    {
        return $this->compiler->createDir;
    }

    /**
     * Retourne le chemin de base
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Compile les services en une classe unique
     */
    public function compile(): void
    {
        if (!$this->cacheEnabled) {
            return;
        }

        $compiler = new ServiceCompiler($this->cacheEnabled, $this->cacheDir, $this->basePath);
        $compiler->compile($this);
        
        require_once $compiler->getCacheFile();
        $this->compiled = true;
    }

    /**
     * Charge la classe compilée si elle existe
     */
    public function loadCompiled(): void
    {
        if (!$this->cacheEnabled) {
            return;
        }

        $compiler = new ServiceCompiler($this->cacheEnabled, $this->cacheDir, $this->basePath);
        $compiledFile = $compiler->getCacheFile();
        
        if (file_exists($compiledFile)) {
            require_once $compiledFile;
            $this->compiled = true;
        }
    }

    /**
     * Vérifie si les services sont compilés
     */
    public function isCompiled(): bool
    {
        return $this->compiled;
    }

    /**
     * Récupère un service compilé
     */
    private function getCompiled(string $id): mixed
    {
        if (!$this->cacheEnabled) {
            throw new ContainerException('Le cache est désactivé');
        }

        if (!$this->compiled) {
            throw new ContainerException('Les services ne sont pas compilés');
        }

        $methodName = 'resolve' . $this->normalizeAlias($id);
        if (!method_exists('CompiledServices', $methodName)) {
            throw new ContainerException("La méthode $methodName n'existe pas dans la classe compilée");
        }

        return \CompiledServices::$methodName();
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