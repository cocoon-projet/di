<?php
declare(strict_types=1);

namespace Tests\Injection\Autowire;

use Tests\Injection\Autowire\C;

class A
{
    public function __construct(
        public B $b,
        public C $c
    ) {}
}