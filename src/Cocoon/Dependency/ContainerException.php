<?php
declare(strict_types=1);

namespace Cocoon\Dependency;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

/**
 * Exception générique du container
 */
class ContainerException extends RuntimeException implements ContainerExceptionInterface
{
}
