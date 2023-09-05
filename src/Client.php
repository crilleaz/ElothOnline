<?php

declare(strict_types=1);

namespace Game;

use Game\Auth\AuthService;
use Game\Player\CharacterRepository;
use Game\Player\Player;
use Game\UI\Scene;
use Game\UI\Scene\Input\HttpInput;

readonly class Client
{
    private const SCENE_AUTH       = 'auth';
    private const SCENE_DUNGEONS   = 'dungeons';
    private const SCENE_HIGH_SCORE = 'highscores';
    private const SCENE_MAIN       = 'main';
    private const SCENE_INVENTORY  = 'inventory';
    private const SCENE_LIBRARY    = 'library';
    private const SCENE_SHOPS      = 'shops';

    private const SCENE_SHOP               = 'shop';
    private const SCENE_CHARACTER_CREATION = 'charCreation';

    private const SCENE_ACTIVITY = 'activity';

    private const CONTROLLERS = [
        self::SCENE_AUTH               => Scene\Auth::class,
        self::SCENE_CHARACTER_CREATION => Scene\CharacterCreation::class,
        self::SCENE_MAIN               => Scene\MainMenu::class,
        self::SCENE_DUNGEONS           => Scene\Dungeons::class,
        self::SCENE_ACTIVITY           => Scene\Activity::class,
        self::SCENE_LIBRARY            => Scene\Library::class,
        self::SCENE_HIGH_SCORE         => Scene\Highscore::class,
        self::SCENE_INVENTORY          => Scene\Inventory::class,
        self::SCENE_SHOPS              => Scene\Shops::class,
        self::SCENE_SHOP               => Scene\Shop::class,
    ];

    public function __construct(
        private AuthService $authService,
        private CharacterRepository $characterRepository
    ) {
    }

    public function run(): void
    {
        $userInput = new HttpInput();
        if (!$this->isSignedIn()) {
            $scene = $this->getScene(self::SCENE_AUTH);
        } elseif ($this->getCurrentPlayer() === null) {
            $scene = $this->getScene(self::SCENE_CHARACTER_CREATION);
        } elseif (isset($_GET['scene']) && is_string($_GET['scene'])) {
            $scene = $this->getScene($_GET['scene']);
        } else {
            $scene = $this->getScene(self::SCENE_MAIN);
        }

        echo $scene->run($userInput);
    }

    public function getCurrentPlayer(): ?Player
    {
        $currentUser = $this->authService->getCurrentUser();
        if ($currentUser === null) {
            return null;
        }

        return $this->characterRepository->findByUser($currentUser);
    }

    private function isSignedIn(): bool
    {
        $currentUser = $this->authService->getCurrentUser();

        return $currentUser !== null;
    }

    private function getScene(string $scene): Scene\SceneInterface
    {
        return \DI::getService(self::CONTROLLERS[$scene]);
    }
}
