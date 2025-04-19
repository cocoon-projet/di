<?php
declare(strict_types=1);

namespace Tests\Injection\Core\Services;

use Tests\Injection\Core\Interfaces\LoggerInterface;

class UserService
{
    private LoggerInterface $logger;
    private array $config = [];
    private ?DatabaseService $database = null;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setDatabase(DatabaseService $database): void
    {
        $this->database = $database;
    }

    public function getDatabase(): ?DatabaseService
    {
        return $this->database;
    }
} 