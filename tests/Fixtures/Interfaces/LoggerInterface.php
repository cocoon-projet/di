<?php
declare(strict_types=1);

namespace Tests\Fixtures\Interfaces;

interface LoggerInterface {
    public function log(string $message): void;
} 