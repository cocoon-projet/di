<?php


use Cocoon\Dependency\Container;
use Injection\Core\ItemController;
use PHPUnit\Framework\TestCase;

class ContainerFileServicesDefinitionsTest extends TestCase
{
    private $service;

    public function setUp()
    {
        $this->service = Container::getInstance();
    }

    public function testFileServicesDefinitions()
    {
        $this->service->addServices(__DIR__ . '/Injection/Core/config.php');
        $this->assertEquals('mysql:host=localhost;dbname=testdb', $this->service->get('db.dsn'));
        $this->assertInstanceOf(ItemController::class, $this->service->get('item'));
    }
}