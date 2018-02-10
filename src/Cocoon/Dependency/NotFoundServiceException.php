<?php
namespace Cocoon\Dependency;

use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

/**
 * Class NotFoundServiceException
 * @package Dependency
 */
class NotFoundServiceException extends InvalidArgumentException implements NotFoundExceptionInterface
{

}
