<?php
declare(strict_types=1);

namespace Tests\Fixtures;

use Cocoon\Dependency\Features\Attributes\Inject;

class InvalidService {
    #[Inject]
    private $undefined;
} 