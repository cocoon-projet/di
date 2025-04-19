<?php
declare(strict_types=1);

namespace Tests\Injection\Core\Interfaces;

interface FactoryInterface
{
    public function create(string $host, string $name): object;
} 