<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Logger;
use Tests\Fixtures\CustomLogger;
use Tests\Fixtures\UserRepository;
use Tests\Fixtures\UserService;
use Tests\Fixtures\InvalidService;
use Tests\Fixtures\Interfaces\LoggerInterface;
use Tests\Fixtures\Interfaces\UserRepositoryInterface;

class ContainerAttributesTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->reset();
    }

    public function testInjectionParType(): void
    {
        // Configuration des services
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', CustomLogger::class);
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);

        // Configuration du service avec injection par attributs
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@inject' => true
        ]);

        // Récupération et test du service
        $userService = $this->container->get(UserService::class);
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
        $this->assertInstanceOf(CustomLogger::class, $userService->getCustomLogger());
        $this->assertInstanceOf(UserRepository::class, $userService->getRepository());
    }

    public function testInjectionParNomDeService(): void
    {
        // Configuration des services
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', CustomLogger::class);
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);

        // Configuration du service avec injection par attributs
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@inject' => true
        ]);

        // Récupération et test du service
        $userService = $this->container->get(UserService::class);
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(CustomLogger::class, $userService->getCustomLogger());
    }

    public function testInjectionDansConstructeur(): void
    {
        // Configuration des services
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', CustomLogger::class);
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);

        // Configuration du service avec injection par attributs
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@inject' => true
        ]);

        // Récupération et test du service
        $userService = $this->container->get(UserService::class);
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(UserRepository::class, $userService->getRepository());
    }

    public function testErreurServiceNonDefini(): void
    {
        $this->expectException(\Cocoon\Dependency\ContainerException::class);
        $this->expectExceptionMessage('Le service Tests\Fixtures\Interfaces\UserRepositoryInterface n\'est pas défini');

        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@inject' => true
        ]);
        $this->container->get(UserService::class);
    }

    public function testErreurTypeNonDefini(): void
    {
        $this->expectException(\Cocoon\Dependency\ContainerException::class);
        $this->expectExceptionMessage('Le type de la propriété undefined n\'est pas défini');

        $this->container->bind(InvalidService::class, [
            '@class' => InvalidService::class,
            '@inject' => true
        ]);
        $this->container->get(InvalidService::class);
    }
} 