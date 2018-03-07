<?php
namespace Injection;


use Cocoon\Dependency\Container;
use Injection\Autowire\B;
use Injection\Autowire\D;
use Injection\Core\ItemController;
use PHPUnit\Framework\TestCase;

class ContainerArrayServicesDefinitions extends TestCase
{
    private $service;

    public function setUp()
    {
        $this->service = Container::getInstance();
        $this->service->addservices([
            'db.dsn' => 'mysql:host=localhost;dbname=testdb',
            'db.port' => 3306,
            'app.config' => ['mode' => 'production', 'debug' => false],
            'item' => 'Injection\Core\ItemController',
            // alias => null   (l'alias  est le service)
            D::class => null,
            B::class => ['@constructor' => [D::class]]
        ]);
    }

    public function testArrayServicesConfiguration()
    {
        $this->assertEquals('mysql:host=localhost;dbname=testdb', $this->service->get('db.dsn'));
        $this->assertInstanceOf(ItemController::class, $this->service->get('item'));
        $this->assertInstanceOf(B::class, $this->service->get(B::class));
    }

}