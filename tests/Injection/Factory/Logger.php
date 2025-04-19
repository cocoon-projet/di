<?php
declare(strict_types=1);

namespace Tests\Injection\Factory;

class Logger
{
    private string $channel;

    public function __construct(string $channel)
    {
        $this->channel = $channel;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
} 