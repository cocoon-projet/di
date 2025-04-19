<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Cocoon\Dependency\ContainerException;
use Cocoon\Dependency\Features\Attributes\Inject;
use PHPUnit\Framework\TestCase;

class ContainerAttributesInjectionTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testPropertyInjectionByType(): void
    {
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', Logger::class);
        
        $service = $this->container->injectAttributes(PropertyInjectionService::class);
        
        $this->assertInstanceOf(Logger::class, $service->getLogger());
        $this->assertInstanceOf(Logger::class, $service->getCustomLogger());
    }

    public function testPropertyInjectionByServiceName(): void
    {
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', Logger::class);
        
        $service = $this->container->injectAttributes(PropertyInjectionService::class);
        
        $this->assertInstanceOf(Logger::class, $service->getCustomLogger());
    }

    public function testConstructorParameterInjection(): void
    {
        $this->container->bind(RepositoryInterface::class, Repository::class);
        
        $service = $this->container->injectAttributes(ConstructorInjectionService::class);
        
        $this->assertInstanceOf(Repository::class, $service->getRepository());
    }

    public function testMissingServiceException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Le service Tests\LoggerInterface n\'est pas défini');
        
        $this->container->injectAttributes(PropertyInjectionService::class);
    }

    public function testUndefinedTypeException(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Le type de la propriété undefined n\'est pas défini');
        
        $this->container->injectAttributes(UndefinedTypeService::class);
    }
}

// Classes de test
interface LoggerInterface
{
    public function log(string $message): void;
}

class Logger implements LoggerInterface
{
    public function log(string $message): void
    {
        // Implementation
    }
}

interface RepositoryInterface
{
    public function find(int $id): array;
}

class Repository implements RepositoryInterface
{
    public function find(int $id): array
    {
        return [];
    }
}

class PropertyInjectionService
{
    #[Inject]
    private LoggerInterface $logger;

    #[Inject('custom.logger')]
    private LoggerInterface $customLogger;

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getCustomLogger(): LoggerInterface
    {
        return $this->customLogger;
    }
}

class ConstructorInjectionService
{
    public function __construct(
        #[Inject]
        private RepositoryInterface $repository
    ) {
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}

class UndefinedTypeService
{
    #[Inject]
    private $undefined;
} 