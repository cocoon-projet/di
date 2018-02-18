<?php

namespace Injection;

use Cocoon\Dependency\Container;
use Injection\Proxy\MyProxyClass;
use Injection\Proxy\TestProxy;
use PHPUnit\Framework\TestCase;

class ContainerLazyInjectionTest extends TestCase
{
    private $service;

    public function setUp()
    {
        $this->service = Container::getInstance();
        $this->service->lazy(MyProxyClass::class);
        $this->service->bind(TestProxy::class, ['@constructor' => [MyProxyClass::class]]);
    }

    public function testlazyInjectionService()
    {
        $proxy = $this->service->get(TestProxy::class);
        $this->assertInstanceOf(MyProxyClass::class, $proxy->proxy);
    }
}
