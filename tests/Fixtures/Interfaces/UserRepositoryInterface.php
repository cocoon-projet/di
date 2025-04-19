<?php
declare(strict_types=1);

namespace Tests\Fixtures\Interfaces;

interface UserRepositoryInterface {
    public function find(int $id): array;
} 