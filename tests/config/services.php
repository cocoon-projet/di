<?php
declare(strict_types=1);

use Injection\Core\Services\{
    UserService,
    Logger,
    DatabaseService
};
use Injection\Core\Interfaces\LoggerInterface;

return [
    LoggerInterface::class => Logger::class,
    
    UserService::class => [
        '@constructor' => [
            'logger' => LoggerInterface::class
        ],
        '@methods' => [
            'setDatabase' => [DatabaseService::class]
        ]
    ],
    
    DatabaseService::class => [
        '@constructor' => [
            'config' => [
                'host' => 'localhost',
                'name' => 'testdb'
            ]
        ]
    ],
    
    'app.config' => [
        'env' => 'test',
        'debug' => true
    ]
]; 