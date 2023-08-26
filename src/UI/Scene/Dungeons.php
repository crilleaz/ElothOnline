<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Engine\Error;
use Game\Game;
use Twig\Environment;

class Dungeons extends AbstractScene
{
    private string $infoMsg = '';
    private string $errorMsg = '';

    public function __construct(
        Game $game,
        Environment $renderer,
        private readonly DungeonRepository $dungeonRepository
    ) {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(): string
    {
        $this->handleInteraction();

        return $this->renderTemplate('dungeons', [
            'dungeons' => $this->dungeonRepository->listDungeons(),
            'errorMsg' => $this->errorMsg,
            'infoMsg' => $this->infoMsg,
        ]);
    }

    private function handleInteraction(): void
    {
        if (isset($_GET['hunt'])) {
            $selectedDungeon = (int)$_GET['hunt'];
            $dungeon = $this->dungeonRepository->findById($selectedDungeon);

            $result = $this->getCurrentPlayer()->enterDungeon($dungeon);
            if ($result instanceof Error) {
                $this->errorMsg = $result->message;
            } else {
                $this->infoMsg = 'You started hunting.';
            }

            return;
        }

        if (isset($_GET['leave'])) {
            $this->getCurrentPlayer()->leaveDungeon();
            $this->infoMsg = 'You left the dungeon';
        }
    }
}
