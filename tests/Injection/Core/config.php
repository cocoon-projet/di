<?php
return [
    'db.dsn' => 'mysql:dbname=blog;host=localhost;charset=utf8',
    'db.user' => 'root',
    'db.password' => '',
    \PDO::class => [
        'constructor' => ['db.dsn', 'db.user', 'db.password']
    ]
];