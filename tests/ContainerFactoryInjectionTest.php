<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Tests\Injection\Factory\DatabaseFactory;
use Tests\Injection\Factory\LoggerFactory;
use PHPUnit\Framework\TestCase;

class ContainerFactoryInjectionTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testFactoryInjection(): void
    {
        // Test factory avec méthode statique
        $this->container->factory('database', [
            DatabaseFactory::class,
            'create'
        ], [
            'host' => 'localhost',
            'name' => 'testdb'
        ]);

        // Test factory avec méthode d'instance
        $this->container->factory('logger', [
            LoggerFactory::class,
            'createLogger'
        ], [
            'channel' => 'test'
        ]);

        $db = $this->container->get('database');
        $logger = $this->container->get('logger');

        $this->assertEquals('localhost', $db->getHost());
        $this->assertEquals('testdb', $db->getName());
        $this->assertEquals('test', $logger->getChannel());
    }
}