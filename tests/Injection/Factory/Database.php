<?php
declare(strict_types=1);

namespace Tests\Injection\Factory;

class Database
{
    private string $host;
    private string $name;

    public function __construct(string $host, string $name)
    {
        $this->host = $host;
        $this->name = $name;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getName(): string
    {
        return $this->name;
    }
} 