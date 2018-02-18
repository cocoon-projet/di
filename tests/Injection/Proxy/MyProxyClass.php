<?php
/**
 * Created by PhpStorm.
 * User: Fpilucius
 * Date: 18/02/2018
 * Time: 11:01
 */

namespace Injection\Proxy;


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