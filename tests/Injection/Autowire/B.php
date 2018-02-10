<?php

namespace Injection\Autowire;


class B
{
    /**
     * @var D
     */
    public $d;

    public function __construct(D $d)
    {
        $this->d = $d;
    }

}