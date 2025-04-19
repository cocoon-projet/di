<?php
declare(strict_types=1);

namespace Tests;

use Cocoon\Dependency\DI;
use Tests\Injection\Autowire\D;
use Tests\Injection\Autowire\B;
use PHPUnit\Framework\TestCase;

class ContainerDiClassTest extends TestCase
{
    protected function setUp(): void
    {
        // Réinitialiser le container entre chaque test
        DI::getInstance()->getServices() === [] || DI::reset();
    }

    public function testDiFacadeClass(): void
    {
        // Configuration des services
        DI::addServices([
            'db.dsn' => 'mysql:host=localhost;dbname=testdb',
            'db.port' => 3306,
            'app.config' => ['mode' => 'production', 'debug' => false],
        ]);

        // Test des bindings
        DI::bind(D::class);
        DI::bind(B::class, [
            '@class' => B::class,
            '@constructor' => [D::class]
        ]);

        // Assertions
        $this->assertEquals('mysql:host=localhost;dbname=testdb', DI::get('db.dsn'));
        $this->assertInstanceOf(B::class, DI::get(B::class));
    }
}