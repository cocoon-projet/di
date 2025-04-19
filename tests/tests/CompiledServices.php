<?php
declare(strict_types=1);

class CompiledServices
{
    private static array $singletons = [];
    private static array $services = [];

    public static function initialize(array $services): void
    {
        self::$services = $services;
    }

    private static function initializeSimpleServices(): void
    {
        self::$services['Tests\Fixtures\Interfaces\LoggerInterface'] = 'Tests\\Fixtures\\Logger';
        self::$services['custom.logger'] = 'Tests\\Fixtures\\CustomLogger';
        self::$services['Tests\Fixtures\Interfaces\UserRepositoryInterface'] = 'Tests\\Fixtures\\UserRepository';
    }

    private static function getSimpleService(string $alias): mixed
    {
        if (!isset(self::$services[$alias])) {
            self::initializeSimpleServices();
            if (!isset(self::$services[$alias])) {
                throw new \RuntimeException('Service ' . $alias . ' not found');
            }
        }
        return self::$services[$alias];
    }

    public static function resolveLoggerInterface()
    {
        if (isset(self::$singletons['Tests\Fixtures\Interfaces\LoggerInterface'])) {
            return self::$singletons['Tests\Fixtures\Interfaces\LoggerInterface'];
        }

        $instance = new \Tests\Fixtures\Logger();
        return $instance;
    }

    public static function resolveCustomLogger()
    {
        if (isset(self::$singletons['custom.logger'])) {
            return self::$singletons['custom.logger'];
        }

        $instance = new \Tests\Fixtures\CustomLogger();
        return $instance;
    }

    public static function resolveUserRepositoryInterface()
    {
        if (isset(self::$singletons['Tests\Fixtures\Interfaces\UserRepositoryInterface'])) {
            return self::$singletons['Tests\Fixtures\Interfaces\UserRepositoryInterface'];
        }

        $instance = new \Tests\Fixtures\UserRepository();
        return $instance;
    }

    public static function resolveUserService()
    {
        if (isset(self::$singletons['Tests\Fixtures\UserService'])) {
            return self::$singletons['Tests\Fixtures\UserService'];
        }

        $instance = new \Tests\Fixtures\UserService(self::resolveUserRepositoryInterface());
        $reflection = new \ReflectionClass('Tests\Fixtures\UserService');
        $property = $reflection->getProperty('logger');
        $property->setAccessible(true);
        $property->setValue($instance, self::resolveLoggerInterface());
        $reflection = new \ReflectionClass('Tests\Fixtures\UserService');
        $property = $reflection->getProperty('customLogger');
        $property->setAccessible(true);
        $property->setValue($instance, self::resolveCustomLogger());
        $reflection = new \ReflectionClass('Tests\Fixtures\UserService');
        $property = $reflection->getProperty('repository');
        $property->setAccessible(true);
        $property->setValue($instance, self::resolveUserRepositoryInterface());
        return $instance;
    }

}
