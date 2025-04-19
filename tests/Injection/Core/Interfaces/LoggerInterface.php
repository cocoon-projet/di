<?php
declare(strict_types=1);

namespace Tests\Injection\Core\Interfaces;

interface LoggerInterface
{
    public function log(string $message): void;
} 