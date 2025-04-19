<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Cocoon\Dependency\Container;
use Tests\Injection\Proxy\TestProxy;
use Tests\Injection\Lazy\HeavyService;
use Tests\Injection\Proxy\MyProxyClass;
use Tests\Injection\Proxy\AnotherProxyClass;

class ContainerLazyInjectionTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
        $this->container->lazy(MyProxyClass::class);
        $this->container->lazy(AnotherProxyClass::class, ['param_1', 'param_2']);
        $this->container->bind(TestProxy::class, [
            '@class' => TestProxy::class,
            '@constructor' => [MyProxyClass::class, AnotherProxyClass::class]
        ]);
    }

    public function testLazyLoading(): void
    {
        $this->container->lazy(HeavyService::class, [
            'config' => ['cache' => true]
        ]);

        $service = $this->container->get(HeavyService::class);
        
        $this->assertInstanceOf(HeavyService::class, $service);
        $this->assertTrue($service->getConfig()['cache']);
    }

    public function testlazyInjectionService()
    {
        $proxy = $this->container->get(TestProxy::class);
        $this->assertInstanceOf(MyProxyClass::class, $proxy->proxy);
    }

    public function testlazyInjectionServiceWithConstructorParams()
    {
        $proxy_args = $this->container->get(TestProxy::class);
        $this->assertEquals('param_1', $proxy_args->getAnotherProxyArg());
    }
}
