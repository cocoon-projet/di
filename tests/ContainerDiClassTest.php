<?php

use Cocoon\Dependency\DI;
use Injection\Autowire\D;
use Injection\Autowire\B;
use PHPUnit\Framework\TestCase;

class ContainerDiClassTest extends TestCase
{
    public function testDiFacadeClass()
    {
        DI::addServices([
            'db.dsn' => 'mysql:host=localhost;dbname=testdb',
            'db.port' => 3306,
            'app.config' => ['mode' => 'production', 'debug' => false],
        ]);
        DI::bind(D::class);
        DI::bind(B::class,[
            '@constructor' => [D::class]
        ]);
        $this->assertEquals(DI::get('db.dsn'), 'mysql:host=localhost;dbname=testdb');
        $this->assertInstanceOf(B::class, DI::get(B::class));
    }
}