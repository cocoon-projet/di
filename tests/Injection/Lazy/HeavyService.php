<?php
declare(strict_types=1);

namespace Tests\Injection\Lazy;

class HeavyService
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }
} 