<?php

namespace Tests\Injection\Proxy;


class MyProxyClass
{
    public function __construct()
    {
        var_dump('connexion proxy');
    }
    public function render()
    {
        return 'content';
    }
}