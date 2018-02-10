<?php

namespace Injection;

use Cocoon\Dependency\Container;
use Injection\Core\SingController;
use PHPUnit\Framework\TestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class ContainerLazyInjectionTest extends TestCase
{
    private $service;

    public function setUp()
    {
        $this->service = Container::getInstance();
        $this->service->lazy(SingController::class);
    }

    public function testlazyInjectionService()
    {
        $proxy = $this->service->get(SingController::class);
        $this->assertInstanceOf(SingController::class, $proxy);
        $proxy->setkey('ProxyManager');
        $this->assertEquals('ProxyManager', $proxy->getKey());
    }
}