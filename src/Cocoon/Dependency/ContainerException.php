<?php
namespace Cocoon\Dependency;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * Class ContainerException
 * @package Dependency
 */
class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
