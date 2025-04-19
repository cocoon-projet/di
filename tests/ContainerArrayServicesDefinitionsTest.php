<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\Container;
use Tests\Injection\Core\Services\UserService;
use Tests\Injection\Core\Services\Logger;
use Tests\Injection\Core\Interfaces\LoggerInterface;
use PHPUnit\Framework\TestCase;

class ContainerArrayServicesDefinitionsTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->reset();
    }

    public function testArrayServicesDefinitions(): void
    {
        $services = [
            LoggerInterface::class => Logger::class,
            UserService::class => [
                '@class' => UserService::class,
                '@constructor' => [
                    LoggerInterface::class
                ],
                '@methods' => [
                    'setConfig' => [['debug' => true]]
                ]
            ],
            'app.config' => [
                'env' => 'test',
                'debug' => true
            ]
        ];

        $this->container->addServices($services);

        $userService = $this->container->get(UserService::class);
        $services = $this->container->getServices();
        $this->assertIsArray($services);
        $this->assertEquals(count($services), 3);

        $this->assertInstanceOf(UserService::class, $userService);
        $this->assertInstanceOf(Logger::class, $userService->getLogger());
        $this->assertTrue($this->container->get('app.config')['debug']);
    }
}