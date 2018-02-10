<?php
namespace Injection\Autowire;

class A
{
    /**
     * @var B
     */
    public $b;
    /**
     * @var C
     */
    public $c;

    public function __construct(B $b, C $c)
    {
        $this->b = $b;
        $this->c = $c;
    }
}