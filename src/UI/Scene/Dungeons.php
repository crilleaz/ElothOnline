<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Engine\Error;
use Game\Game;
use Game\Wiki;
use Twig\Environment;

class Dungeons extends AbstractScene
{
    private string $infoMsg = '';
    private string $errorMsg = '';

    public function __construct(Game $game, Environment $renderer, private readonly Wiki $wiki)
    {
        parent::__construct($game, $renderer);
    }

    public function run(): string
    {
        $this->handleInteraction();

        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('dungeons', [
            'player' => $player,
            'dungeons' => $this->wiki->getDungeons(),
            'errorMsg' => $this->errorMsg,
            'infoMsg' => $this->infoMsg,
        ]);
    }

    private function handleInteraction(): void
    {
        if (isset($_GET['hunt'])) {
            $selectedDungeon = (int)$_GET['hunt'];

            $result = $this->game->getCurrentPlayer()->enterDungeon($selectedDungeon);
            if ($result instanceof Error) {
                $this->errorMsg = $result->message;
            } else {
                $this->infoMsg = 'You started hunting.';
            }

            return;
        }

        if (isset($_GET['leave'])) {
            $this->game->getCurrentPlayer()->leaveDungeon();
            $this->infoMsg = 'You left the dungeon';
        }
    }
}
