<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Tests\Injection\Core\Services\{
    UserService,
    Logger,
    ConfigService,
    DatabaseService
};
use Tests\Injection\Core\Interfaces\LoggerInterface;
use PHPUnit\Framework\TestCase;

class ContainerInjectionAliasObjectTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testSimpleClassInjection(): void
    {
        $this->container->bind(Logger::class);
        $logger = $this->container->get(Logger::class);
        
        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function testInterfaceInjection(): void
    {
        $this->container->bind(LoggerInterface::class, Logger::class);
        $logger = $this->container->get(LoggerInterface::class);
        
        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }

    public function testConstructorInjection(): void
    {
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@constructor' => [
                'logger' => LoggerInterface::class
            ]
        ]);

        $userService = $this->container->get(UserService::class);
        
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
    }

    public function testMethodInjection(): void
    {
        $this->container->bind(ConfigService::class, [
            '@class' => ConfigService::class,
            '@methods' => [
                'setConfig' => [['debug' => true]],
                'setEnvironment' => ['production']
            ]
        ]);

        $config = $this->container->get(ConfigService::class);
        
        $this->assertTrue($config->getConfig()['debug']);
        $this->assertEquals('production', $config->getEnvironment());
    }

    public function testComplexInjection(): void
    {
        // Configuration des services
        $this->container->bind(LoggerInterface::class, Logger::class);
        $this->container->bind(DatabaseService::class, [
            '@class' => DatabaseService::class,
            '@constructor' => [
                'config' => [
                    'host' => 'localhost',
                    'name' => 'testdb'
                ]
            ]
        ]);
        
        $this->container->bind(UserService::class, [
            '@class' => UserService::class,
            '@constructor' => [
                'logger' => LoggerInterface::class
            ],
            '@methods' => [
                'setDatabase' => [DatabaseService::class],
                'setConfig' => [['cache' => true]]
            ]
        ]);

        // Récupération et tests
        $userService = $this->container->get(UserService::class);
        
        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
        $this->assertInstanceOf(DatabaseService::class, $userService->getDatabase());
        $this->assertTrue($userService->getConfig()['cache']);
        $this->assertEquals('localhost', $userService->getDatabase()->getConfig()['host']);
    }

    public function testSingletonInjection(): void
    {
        $this->container->singleton(ConfigService::class);
        
        $config1 = $this->container->get(ConfigService::class);
        $config2 = $this->container->get(ConfigService::class);
        
        $this->assertSame($config1, $config2);
    }

    public function testAliasInjection(): void
    {
        $this->container->bind('logger', Logger::class);
        $this->container->bind('config', [
            'debug' => true,
            'env' => 'test'
        ]);

        $logger = $this->container->get('logger');
        $config = $this->container->get('config');
        
        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertTrue($config['debug']);
        $this->assertEquals('test', $config['env']);
    }
}
