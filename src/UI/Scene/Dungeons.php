<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\DungeonRepository;
use Game\Engine\Error;
use Game\Client;
use Game\UI\Scene\Input\InputInterface;
use Twig\Environment;

class Dungeons extends AbstractScene
{
    private string $infoMsg  = '';
    private string $errorMsg = '';

    public function __construct(
        Client $game,
        Environment $renderer,
        private readonly DungeonRepository $dungeonRepository
    ) {
        parent::__construct($game, $renderer, $dungeonRepository);
    }

    public function run(InputInterface $input): string
    {
        $this->handleInteraction($input);

        return $this->renderTemplate('dungeons', [
            'dungeons' => $this->dungeonRepository->listDungeons(),
            'errorMsg' => $this->errorMsg,
            'infoMsg'  => $this->infoMsg,
        ]);
    }

    private function handleInteraction(InputInterface $input): void
    {
        $selectedDungeon = $input->getInt('hunt');
        if ($selectedDungeon !== 0) {
            $dungeon = $this->dungeonRepository->getById($selectedDungeon);

            $result = $this->getCurrentPlayer()->enterDungeon($dungeon);
            if ($result instanceof Error) {
                $this->errorMsg = $result->message;
            } else {
                $this->infoMsg = 'You started hunting.';
            }

            return;
        }

        $leaveDungeon = $input->getString('leave');
        if ($leaveDungeon) {
            $this->getCurrentPlayer()->leaveDungeon();
            $this->infoMsg = 'You left the dungeon';
        }
    }
}
