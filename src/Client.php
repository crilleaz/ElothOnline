<?php
declare(strict_types=1);

namespace Game;

use Game\Auth\AuthService;
use Game\Player\CharacterRepository;
use Game\Player\Player;

readonly class Client
{
    public function __construct(
        private AuthService $authService,
        private CharacterRepository $characterRepository
    ){}

    public function run(): void
    {
        // TODO add self-sufficient front controller handling
    }

    public function getCurrentPlayer(): ?Player
    {
        $currentUser = $this->authService->getCurrentUser();
        if ($currentUser === null) {
            return null;
        }

        return $this->characterRepository->findByUser($currentUser);
    }

    /**
     * Basically pretends to say that client is not running unless user is signed in
     *
     * @todo requires rethinking
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        $currentUser = $this->authService->getCurrentUser();

        return $currentUser !== null;
    }
}
