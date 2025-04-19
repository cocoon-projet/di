<?php

namespace Tests\Injection\Core;


class ItemStaticFactory
{

    public static function getItem()
    {
        return (new ItemController())->render();
    }

}