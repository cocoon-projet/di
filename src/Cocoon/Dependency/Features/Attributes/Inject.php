<?php
declare(strict_types=1);

namespace Cocoon\Dependency\Features\Attributes;

use Attribute;

/**
 * Attribut pour l'injection de dépendances
 * 
 * @example
 * #[Inject] // Injection par type
 * private LoggerInterface $logger;
 * 
 * #[Inject('database.connection')] // Injection par nom de service
 * private Database $db;
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class Inject
{
    public function __construct(
        private ?string $service = null
    ) {
    }

    public function getService(): ?string
    {
        return $this->service;
    }
} 