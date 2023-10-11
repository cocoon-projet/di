<?php

use Injection\Core\Singleton;
use Injection\Core\ItemController;
use Injection\Core\PostController;
use Injection\Core\SingController;
use Cocoon\Dependency\Container;
use PHPUnit\Framework\TestCase;

class containerInjectionAliasObjectTest extends TestCase
{
    private $service;

    protected function setUp() :void
    {
        $this->service = Container::getInstance();
        $this->service->bind('key.class', ItemController::class);
        $this->service->bind('class.params', [
            '@class' => PostController::class,
            '@constructor' => ['je suis le param un', 'je suis le param deux']
        ]);
        $this->service->bind('class.methods', [
            '@class' => ItemController::class,
            '@methods' => [
                'setName' => ['Doe'],
                'setSurname' => ['John']
            ]
        ]);
        $this->service->bind('callable.key', function () {
            return 'je suis un callable';
        });
        $this->service->bind('singleton', [
            '@class' => SingController::class,
            '@singleton' => true
        ]);
        $this->service->bind('patternn.singleton', Singleton::getInstance());
        $this->service->bind('patternn.singleton.name', Singleton::class);
        $this->service->bind('class.name', 'Injection\Core\ItemController');
        $this->service->bind('class.instance', new ItemController());
    }

    /**
     * Retourne une instance de ItemController::class definit par l'alias key.class
     * Retourne toujours une nouvelle instance de la class
     */
    public function testInjectClass ()
    {
        $this->assertInstanceOf(ItemController::class, $this->service->get('key.class'));
    }

    /**
     * Retourne une instance de PostController::class avec les paramètres du constructeur définient
     */
    public function testInjectClassWithConstructorParameters ()
    {
        $test = $this->service->get('class.params');
        $this->assertInstanceOf(PostController::class, $test);
        $this->assertEquals($test->getParamUn(), 'je suis le param un');
        $this->assertEquals($test->getParamDeux(), 'je suis le param deux');
    }

    /**
     * Retourne une instance de ItemController::class avec les paramètres des méthodes définient
     */
    public function testInjectClassWithMethodsParameters ()
    {
        $test = $this->service->get('class.methods');
        $this->assertInstanceOf(ItemController::class, $test);
        $this->assertEquals($test->getName(), 'Doe');
        $this->assertEquals($test->getSurname(), 'John');
    }

    /**
     * Closure Injection
     */
    public function testInjectCallableMethod ()
    {
        $test = $this->service->get('callable.key');
        $this->assertTrue(is_callable($this->service->getServices()['callable.key']));
        $this->assertEquals($test, 'je suis un callable');
    }

    /**
     * Injection d'une classe comme singleton retourne toujours la même instance
     */
    public function testInjectClassToSingleton ()
    {
        $instance_1 = $this->service->get('singleton');
        $instance_1->setKey('je suis le paramètre de l\'instance un');
        $this->assertInstanceOf(SingController::class, $instance_1);
        $instance_2 = $this->service->get('singleton');
        // Recupération du paramètre de l'instance_1
        $this->assertEquals($instance_2->getKey(), 'je suis le paramètre de l\'instance un');
    }

    /**
     * Retourne une classe implémentant le pattern singleton
     */
    public function testInjectClassSingletonPatternn ()
    {
        $test = $this->service->get('patternn.singleton');
        $this->assertEquals($test->getVar(), 'je suis le patternn Singleton');
    }

    /**
     * Retourne une classe implémentant le pattern singleton en définissant le nom de la classe
     * Résolution automatique d'une classe (patternn singleton) qui possède une méthode getInstance();
     */
    public function testInjectClassSingletonPatternnByClassName ()
    {
        $test = $this->service->get('patternn.singleton.name');
        $this->assertEquals($test->getVar(), 'je suis le patternn Singleton');
    }

    /**
     * Retourne une instance de classe Définit par son nom complet Injection\Core\ItemController
     */
    public function testInjectClassByFullQualifiedName ()
    {
        $this->assertInstanceOf(ItemController::class, $this->service->get('class.name'));
    }

    public function testInjectClassByInstance()
    {
        $inst_1 = $this->service->get('class.instance');
        $inst_2 = $this->service->get('class.instance');
        $result = ($inst_1 === $inst_2);
        $this->assertTrue($result);
    }
}
