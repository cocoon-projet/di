<?php

use Cocoon\Dependency\Container;
use Injection\Autowire\B;
use Injection\Autowire\C;
use Injection\Autowire\D;
use Injection\Autowire\Params;
use Injection\Core\Controllers\BlogController;
use PHPUnit\Framework\TestCase;
use Injection\Autowire\A;

class ContainerAutowiringWithMakeMethodTest extends TestCase
{
    private $service;

    protected function setUp() :void
    {
        $this->service = Container::getInstance();
    }

    public function testAutowiringMultiObjectInjection()
    {
        $A = $this->service->make(A::class);
        $this->assertInstanceOf(A::class, $A);
        $this->assertInstanceOf(B::class, $A->b);
        $this->assertInstanceOf(C::class, $A->c);
        $this->assertInstanceOf(D::class, $A->b->d);
    }

    public function testAutowiringWithParametersConstructor()
    {
        $test = $this->service->make(Params::class, ['name' => 'Doe', 'surname' => 'John']);
        $this->assertInstanceOf(Params::class, $test);
        $this->assertEquals('Doe', $test->getName());
        $this->assertEquals('John', $test->getSurname());
    }

    public function testAutowireWithParametersMethod()
    {
        $test = $this->service->make(Params::class, 'setName', ['name' => 'Doe_2']);
        $this->assertEquals('Doe_2', $test->getName());
    }

    public function testAutowireteWithObjectConstructorParamForMethod()
    {
        $test = $this->service->make(BlogController::class, 'index');
        $this->assertTrue(is_array($test));
        $this->assertEquals('titre 1', $test[0]['titre']);
    }

    public function testAutowireteWithObjectConstructorParamAndParamMethod()
    {
        $test = $this->service->make(BlogController::class, 'getId', ['id' => 2]);
        $this->assertEquals('titre 3', $test['titre']);
    }

    public function testAutowireCombineObjectAndStringParamsForMethod()
    {
        $test = $this->service->make(BlogController::class, 'item', ['append' => '_string']);
        $this->assertEquals('factory_string', $test);
    }

    public function testAutowireCombineObjectAndStringParamsForConstructor()
    {
        $test = $this->service->make(BlogController::class, ['param' => 'je suis un paramètre']);
        $this->assertEquals('je suis un paramètre', $test->param);
    }

    public function testAutowireSimpleClass()
    {
        $test = $this->service->make(D::class, 'test', ['param' => 'ok']);
        $this->assertEquals('ok', $test);
    }

    public function testAutowireSimpleClassWithNoragumentConstructor()
    {
        $test = $this->service->make(C::class);
        $this->assertInstanceOf(C::class, $test);
    }
}