<?php
declare(strict_types=1);

namespace Tests\Injection\Core\Services;

class ConfigService
{
    private array $config = [];
    private string $environment = '';

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
} 