<?php

use Cocoon\Dependency\Container;
use PHPUnit\Framework\TestCase;

class ContainerInjectionStringIntegerArrayTest extends TestCase
{
    protected $service;

    public function setUp ()
    {
        $this->service = Container::getInstance();
        $this->service->bind('string', 'je suis une chaine de carartère');
        $this->service->bind('int', 8888);
        $this->service->bind('array', ['un', 'deux', 'trois', '13']);
        $this->service->bind('array.key', ['name' => 'Doe', 'surname' => 'John', 'age' => 43]);
    }

    /**
     * String injection
     */
    public function testInjectString ()
    {
        $this->assertTrue(is_string($this->service->get('string')));
        $this->assertEquals($this->service->get('string'), 'je suis une chaine de carartère');
    }

    /**
     * Integer injection
     */
    public function testInjectInteger ()
    {
        $this->assertTrue(is_int($this->service->get('int')));
        $this->assertEquals($this->service->get('int'), 8888);
    }

    /**
     * array injection
     */
    public function testInjectArray()
    {
         $array = $this->service->get('array');
         $this->assertTrue(is_array($array));
         $this->assertCount(4, $array);
         $this->assertEquals('trois', $array[2]);
    }

    /**
     * array keys injection
     */
    public function testInjectArrayKeys()
    {
        $array = $this->service->get('array.key');
        $this->assertTrue(is_array($array));
        $this->assertCount(3, $array);
        $this->assertEquals('Doe', $array['name']);
        $this->assertEquals(43, $array['age']);
    }

}