<?php


use Cocoon\Dependency\Container;
use PHPUnit\Framework\TestCase;

class ContainerExceptionTest extends TestCase
{
    private $service;

    public function setUp()
    {
        $this->service = Container::getInstance();
    }

    public function testBindContainerMethodException()
    {
        $this->expectException(Cocoon\Dependency\ContainerException::class);
        $this->service->bind(123);
    }

    public function testGetContainerMethodException()
    {
        $this->expectException(Cocoon\Dependency\NotFoundServiceException::class);
        $this->service->get('none');
    }

    public function testAddserviceContainerMethodException()
    {
        $this->expectException(Cocoon\Dependency\ContainerException::class);
        $this->service->addServices('kkkkk.php');
    }
}