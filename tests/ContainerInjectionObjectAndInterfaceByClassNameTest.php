<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Tests\Injection\Core\Interfaces\LoggerInterface;
use Tests\Injection\Core\Services\Logger;
use Tests\Injection\Core\Services\UserService;
use PHPUnit\Framework\TestCase;

class ContainerInjectionObjectAndInterfaceByClassNameTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testObjectAndInterfaceInjection(): void
    {
        // Binding interface -> implémentation
        $this->container->bind(LoggerInterface::class, Logger::class);
        
        // Service avec dépendance sur l'interface
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@constructor' => [
                'logger' => LoggerInterface::class
            ]
        ]);

        $userService = $this->container->get(UserService::class);
        
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
        $this->assertInstanceOf(LoggerInterface::class, $userService->getLogger());
    }
}