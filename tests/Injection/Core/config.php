<?php
return [
    'db.dsn' => 'mysql:host=localhost;dbname=testdb',
    'db.port' => 3306,
    'app.config' => ['mode' => 'production', 'debug' => false],
    'item' => 'Injection\Core\ItemController',
    // alias => null   (l'alias  est le service)
    D::class =>'@alias',
    B::class => ['@constructor' => [D::class]]
];