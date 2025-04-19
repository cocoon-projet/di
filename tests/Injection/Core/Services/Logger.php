<?php
declare(strict_types=1);

namespace Tests\Injection\Core\Services;

use Tests\Injection\Core\Interfaces\LoggerInterface;

class Logger implements LoggerInterface
{
    private bool $debug = false;

    public function log(string $message): void
    {
        if ($this->debug) {
            echo "[DEBUG] $message\n";
        }
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }
} 