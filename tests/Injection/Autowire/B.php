<?php
declare(strict_types=1);

namespace Tests\Injection\Autowire;

use Tests\Injection\Autowire\D;

class B
{
    public function __construct(
        public D $d
    ) {}
}