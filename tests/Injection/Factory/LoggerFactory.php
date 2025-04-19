<?php
declare(strict_types=1);

namespace Tests\Injection\Factory;

class LoggerFactory
{
    public function createLogger(string $channel): Logger
    {
        return new Logger($channel);
    }
} 