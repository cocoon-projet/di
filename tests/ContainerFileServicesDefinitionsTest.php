<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Cocoon\Dependency\ContainerException;
use PHPUnit\Framework\TestCase;

class ContainerFileServicesDefinitionsTest extends TestCase
{
    private Container $container;
    private string $configFile;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->getServices() === [] || $this->container->reset();
        $this->configFile = __DIR__ . '/config/services.php';
    }

    public function testFileServicesDefinitions(): void
    {
        $this->container->addServices($this->configFile);
        
        $this->assertTrue($this->container->has('app.config'));
        $this->assertEquals('test', $this->container->get('app.config')['env']);
    }

    public function testInvalidConfigFile(): void
    {
        $this->expectException(ContainerException::class);
        $this->container->addServices('non_existent_file.php');
    }
}
