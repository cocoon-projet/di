<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Cocoon\Dependency\Container;
use Tests\Injection\Core\ItemController;
use Tests\Injection\Autowire\{A, B, C, D, Params};
use Tests\Injection\Core\Controllers\BlogController;

class ContainerAutowiringWithMakeMethodTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testAutowiringMultiObjectInjection(): void
    {
        $a = $this->container->make(A::class);
        
        $this->assertInstanceOf(A::class, $a);
        $this->assertInstanceOf(B::class, $a->b);
        $this->assertInstanceOf(C::class, $a->c);
        $this->assertInstanceOf(D::class, $a->b->d);
    }

    public function testAutowiringWithParametersConstructor(): void
    {
        $test = $this->container->make(Params::class, [
            'name' => 'Doe',
            'surname' => 'John'
        ]);
        
        $this->assertInstanceOf(Params::class, $test);
        $this->assertEquals('Doe', $test->getName());
        $this->assertEquals('John', $test->getSurname());
    }

    public function testAutowireWithParametersMethod(): void
    {
        $controller = $this->container->make(Params::class);
        $test = $controller->setName('Doe_2');
        
        $this->assertEquals('Doe_2', $test->getName());
    }

    public function testAutowireWithObjectConstructorParamForMethod(): void
    {
        $test = $this->container->make(BlogController::class,'index');
        
        $this->assertIsArray($test);
        $this->assertEquals('titre 1', $test[0]['titre']);
    }

    public function testAutowireWithObjectConstructorParamAndParamMethod(): void
    {
        $test = $this->container->make(BlogController::class, 'getId', ['id' => 2]);
        
        $this->assertIsArray($test);
        $this->assertEquals('titre 3', $test['titre']);
    }

    public function testAutowireCombineObjectAndStringParamsForMethod(): void
    {
        $test = $this->container->make(BlogController::class, 'item', ['append' => '_string']);
        
        $this->assertEquals('factory_string', $test);
    }

    public function testAutowireCombineObjectAndStringParamsForConstructor(): void
    {
        $test = $this->container->make(BlogController::class, [
            'param' => 'je suis un paramètre'
        ]);
        
        $this->assertEquals('je suis un paramètre', $test->param);
    }

    public function testAutowireSimpleClass(): void
    {
        $test = $this->container->make(D::class, 'test', ['param' => 'ok']);
        
        $this->assertEquals('ok', $test);
    }

    public function testAutowireSimpleClassWithNoArgumentConstructor(): void
    {
        $test = $this->container->make(C::class);
        
        $this->assertInstanceOf(C::class, $test);
    }
}