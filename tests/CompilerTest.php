<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Logger;
use Tests\Fixtures\CustomLogger;
use Tests\Fixtures\UserRepository;
use Tests\Fixtures\UserService;
use Tests\Fixtures\Interfaces\LoggerInterface;
use Tests\Fixtures\Interfaces\UserRepositoryInterface;

class CompilerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->reset();
        $this->container->setCacheConfig(true, 'tests/', __DIR__);
    }

    public function testCompilation(): void
    {
        // Configuration des services
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', CustomLogger::class);
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@inject' => true
        ]);

        // Compilation des services
        $this->container->compile();

        // Vérification que les services sont compilés
        $this->assertTrue($this->container->isCompiled());

        // Récupération des services compilés
        $userService = $this->container->get(UserService::class);
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
        $this->assertInstanceOf(CustomLogger::class, $userService->getCustomLogger());
        $this->assertInstanceOf(UserRepository::class, $userService->getRepository());
    }

    public function testChargementCompile(): void
    {
        // Configuration des services
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind('custom.logger', CustomLogger::class);
        $this->container->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@inject' => true
        ]);

        // Compilation des services
        $this->container->compile();

        // Création d'un nouveau conteneur
        $newContainer = Container::getInstance();
        $newContainer->reset();

        // Chargement des services compilés
        $newContainer->loadCompiled();

        // Vérification que les services sont compilés
        $this->assertTrue($newContainer->isCompiled());

        // Récupération des services compilés
        $userService = $newContainer->get(UserService::class);
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
        $this->assertInstanceOf(CustomLogger::class, $userService->getCustomLogger());
        $this->assertInstanceOf(UserRepository::class, $userService->getRepository());
    }
} 