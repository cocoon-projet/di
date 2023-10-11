<?php
use Cocoon\Dependency\Container;
use Injection\Core\ControllerInterface;
use Injection\Core\Controllers\BlogController;
use Injection\Core\Controllers\BlogModel;
use Injection\Core\itemController;
use Injection\Core\ItemFactory;
use Injection\Core\PostController;
use Injection\Core\SingController;
use PHPUnit\Framework\TestCase;

class ContainerInjectionObjectAndInterfaceByClassNameTest extends TestCase
{
    private $service;

    protected function setUp() :void
    {
        $this->service = Container::getInstance();
        $this->service->bind(ControllerInterface::class, ItemController::class);
        $this->service->factory(ItemController::class, [itemFactory::class, 'getItem']);
        $this->service->factory('item.class', [itemFactory::class, 'getItem'], ['name' => 'rasmus']);
        $this->service->bind(BlogModel::class);
        $this->service->bind(BlogController::class, [
            '@constructor' => [BlogModel::class]
        ]);
        $this->service->singleton(PostController::class);
        $this->service->bind(SingController::class);
    }

    /**
     * Retourne une instance de classe définit par une interface
     */
    public function testInjectClassByInterface ()
    {
        $test = $this->service->get(ControllerInterface::class);
        $this->assertInstanceOf(ItemController::class, $test);
        $test->setName('Napoleon');
        $this->assertEquals('Napoleon', $test->getName());
    }

    public function testInjectClassParamIntoConstructor()
    {
        $test = $this->service->get(BlogController::class)->index();
        $this->assertTrue(is_array($test));
        $this->assertEquals('titre 1', $test[0]['titre']);
    }

    public function testInjectClassNameBySingleton()
    {
        $inst_1 = $this->service->get(PostController::class);
        $inst_2 = $this->service->get(PostController::class);
        $result = ($inst_1 === $inst_2);
        $this->assertTrue($result);
    }

    public function testInjectClassNameByNewInstance()
    {
        $inst_1 = $this->service->get(SingController::class);
        $inst_2 = $this->service->get(SingController::class);
        $result = ($inst_1 === $inst_2);
        $this->assertFalse($result);
    }
}