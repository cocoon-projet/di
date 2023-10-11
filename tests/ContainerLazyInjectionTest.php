<?php

use Cocoon\Dependency\Container;
use Injection\Proxy\AnotherProxyClass;
use Injection\Proxy\MyProxyClass;
use Injection\Proxy\TestProxy;
use PHPUnit\Framework\TestCase;

class ContainerLazyInjectionTest extends TestCase
{
    private $service;

    protected function setUp() :void
    {
        $this->service = Container::getInstance();
        $this->service->lazy(MyProxyClass::class);
        $this->service->lazy(AnotherProxyClass::class, ['param_1', 'param_2']);
        $this->service->bind(TestProxy::class, ['@constructor' => [MyProxyClass::class, AnotherProxyClass::class]]);
    }

    public function testlazyInjectionService()
    {
        $proxy = $this->service->get(TestProxy::class);
        $this->assertInstanceOf(MyProxyClass::class, $proxy->proxy);
    }

    public function testlazyInjectionServiceWithConstructorParams()
    {
        $proxy_args = $this->service->get(TestProxy::class);
        $this->assertEquals('param_1', $proxy_args->getAnotherProxyArg());
    }
}
