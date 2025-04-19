<?php
declare(strict_types=1);

namespace Tests\Injection\Autowire;


class D
{
    public function test($param)
    {
        return $param;
    }
}