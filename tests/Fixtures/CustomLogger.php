<?php
declare(strict_types=1);

namespace Tests\Fixtures;

use Tests\Fixtures\Interfaces\LoggerInterface;

class CustomLogger implements LoggerInterface {
    public function log(string $message): void {
        echo "Custom: " . $message;
    }
} 