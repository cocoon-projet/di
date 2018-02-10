<?php
namespace Injection\Core;

class ItemFactory
{

    public function getItem($name = '')
    {
        return (new ItemController())->render() . ' ' . $name;
    }
}
