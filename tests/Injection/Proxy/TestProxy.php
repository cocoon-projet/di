<?php

namespace Tests\Injection\Proxy;


class TestProxy
{
    /**
     * @var MyProxyClass
     */
    public $proxy;

    public $render;
    /**
     * @var AnotherProxyClass
     */
    public $anotherProxy;

    public function __construct(MyProxyClass $proxy, AnotherProxyClass $anotherProxy)
    {
        $this->proxy = $proxy;
        $this->anotherProxy = $anotherProxy;
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

    public function getAnotherProxyArg()
    {
        return $this->anotherProxy->arg1;
    }
}