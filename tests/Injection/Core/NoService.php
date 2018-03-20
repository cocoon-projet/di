<?php

namespace Injection\Core;


class NoService
{
    private function __construct()
    {
    }

    public static function init()
    {
        return new NoService();
    }
}