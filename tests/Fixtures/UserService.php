<?php
declare(strict_types=1);

namespace Tests\Fixtures;

use Cocoon\Dependency\Features\Attributes\Inject;
use Tests\Fixtures\Interfaces\LoggerInterface;
use Tests\Fixtures\Interfaces\UserRepositoryInterface;

class UserService {
    #[Inject]
    private LoggerInterface $logger;

    #[Inject('custom.logger')]
    private LoggerInterface $customLogger;

    public function __construct(
        #[Inject]
        private UserRepositoryInterface $repository
    ) {}

    public function getLogger(): LoggerInterface {
        return $this->logger;
    }

    public function getCustomLogger(): LoggerInterface {
        return $this->customLogger;
    }

    public function getRepository(): UserRepositoryInterface {
        return $this->repository;
    }

    public function getUser(int $id): array {
        $this->logger->log("Recherche de l'utilisateur $id");
        $this->customLogger->log("Recherche personnalisée de l'utilisateur $id");
        return $this->repository->find($id);
    }
} 