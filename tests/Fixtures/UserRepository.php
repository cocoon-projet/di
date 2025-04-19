<?php
declare(strict_types=1);

namespace Tests\Fixtures;

use Tests\Fixtures\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
    public function find(int $id): array {
        return ['id' => $id, 'name' => 'John Doe'];
    }
} 