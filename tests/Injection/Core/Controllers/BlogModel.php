<?php

namespace Injection\Core\Controllers;


class BlogModel
{
    public function all()
    {
        return [
             0 => [
                 'id' => 1,
                 'titre' => 'titre 1',
                 'content' => 'lorem ipsum 1'
             ],
            1 => [
                'id' => 2,
                'titre' => 'titre 2',
                'content' => 'lorem ipsum 2'
            ],
            2 => [
                'id' => 3,
                'titre' => 'titre 3',
                'content' => 'lorem ipsum 3'
            ]
        ];
    }

}