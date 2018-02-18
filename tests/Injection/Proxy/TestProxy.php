<?php

namespace Injection\Proxy;


class TestProxy
{
    /**
     * @var MyProxyClass
     */
    public $proxy;

    public $render;

    public function __construct(MyProxyClass $proxy)
    {
        $this->proxy = $proxy;
    }

    public function getProxyRender()
    {
        return $this->proxy->render();
    }

    public function notNeedProxy()
    {
       $this->render = 'not need proxy';
       return $this;
    }
}