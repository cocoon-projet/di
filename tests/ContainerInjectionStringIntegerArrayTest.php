<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use PHPUnit\Framework\TestCase;

class ContainerInjectionStringIntegerArrayTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testInjectionStringIntegerArray(): void
    {
        // Test des types scalaires et tableaux
        $services = [
            'db.host' => 'localhost',
            'db.port' => 3306,
            'db.options' => [
                'charset' => 'utf8',
                'timeout' => 5
            ]
        ];

        $this->container->addServices($services);

        $this->assertEquals('localhost', $this->container->get('db.host'));
        $this->assertEquals(3306, $this->container->get('db.port'));
        $this->assertEquals([
            'charset' => 'utf8',
            'timeout' => 5
        ], $this->container->get('db.options'));
    }
}