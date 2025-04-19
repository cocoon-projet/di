<?php
declare(strict_types=1);

namespace Cocoon\Dependency;

use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

/**
 * Exception levée quand un service n'est pas trouvé
 */
class NotFoundServiceException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
