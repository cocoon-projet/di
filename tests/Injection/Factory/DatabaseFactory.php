<?php
declare(strict_types=1);

namespace Tests\Injection\Factory;

use Tests\Injection\Core\Interfaces\FactoryInterface;

class DatabaseFactory implements FactoryInterface
{
    public function create(string $host, string $name): Database
    {
        return new Database($host, $name);
    }
} 