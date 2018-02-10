<?php

namespace Injection\Core;

class SingController implements ControllerInterface
{
    public $key = 'default';
    
    function __construct()
    {

    }

    public function setKey ($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    public function render ()
    {
    
    }    
}