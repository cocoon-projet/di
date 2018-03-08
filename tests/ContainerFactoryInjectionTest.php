<?php

use Cocoon\Dependency\Container;
use Injection\Core\ItemStaticFactory;
use Injection\Core\ItemInvokeFactory;
use PHPUnit\Framework\TestCase;
use Injection\Core\itemController;
use Injection\Core\ItemFactory;

class ContainerFactoryInjectionTest extends TestCase
{
    private $service;

    public function setup()
    {
        $this->service = Container::getInstance();
        $this->service->factory(ItemController::class, [itemFactory::class, 'getItem']);
        $this->service->factory('item.class', [itemFactory::class, 'getItem'], ['name' => 'rasmus']);
        $this->service->factory('item.factory.static', [itemStaticFactory::class, 'getItem']);
        $this->service->factory('item.factory.invoke', [itemInvokeFactory::class]);
    }

    public function testInjectByFactoryMethod()
    {
        $test = $this->service->get(ItemController::class);
        $this->assertEquals('factory ', $test);
    }

    public function testInjectByFactoryMethodAndParam()
    {
        $test = $this->service->get('item.class');
        $this->assertEquals('factory rasmus', $test);
    }

    public function testInjectByFactoryMethodStatic()
    {
        $test = $this->service->get('item.factory.static');
        $this->assertEquals('factory', $test);
    }

    public function testInjectByFactoryMethodInvoke()
    {
        $test = $this->service->get('item.factory.invoke');
        $this->assertEquals('factory', $test);
    }
}