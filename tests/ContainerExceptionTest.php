<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Cocoon\Dependency\ContainerException;
use Cocoon\Dependency\NotFoundServiceException;
use PHPUnit\Framework\TestCase;

class ContainerExceptionTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
    }

    public function testServiceNotFoundException(): void
    {
        $this->expectException(NotFoundServiceException::class);
        $this->container->get('undefined_service');
    }

    public function testInvalidServiceDefinitionException(): void
    {
        $this->expectException(ContainerException::class);
        $this->container->bind('invalid', new \stdClass());
    }

    public function testInvalidFactoryCallableException(): void
    {
        $this->expectException(ContainerException::class);
        $this->container->factory('invalid', ['NonExistentClass', 'method']);
    }
}