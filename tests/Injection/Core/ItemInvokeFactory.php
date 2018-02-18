<?php

namespace Injection\Core;


class ItemInvokeFactory
{

    public function __invoke()
    {
        return (new ItemController())->render();
    }

}