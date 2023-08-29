<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\Dungeon;
use Game\Dungeon\DungeonRepository;
use Game\Game;
use Game\Player\Player;
use Twig\Environment;

abstract class AbstractScene implements SceneInterface
{
    public function __construct(
        protected readonly Game $game,
        private readonly Environment       $renderer,
        private readonly DungeonRepository $dungeonRepository
    ) {

    }

    abstract public function run(): string;

    protected function renderTemplate(string $templateName, array $parameters = []): string
    {
        $fullTemplateName = sprintf('%s.html.twig', $templateName);

        $parameters += [
            'player' => $this->getCurrentPlayer(),
            'huntingDungeon' => $this->getHuntingDungeon(),
        ];

        return $this->renderer->render($fullTemplateName, $parameters);
    }

    /**
     * @param class-string<SceneInterface> $scene
     * @return string
     */
    protected function switchToScene(string $scene): string
    {
        return \DI::getService($scene)->run();
    }

    protected function getCurrentPlayer(): Player
    {
        return $this->game->getCurrentPlayer();
    }

    protected function getHuntingDungeon(): ?Dungeon
    {
        $huntingDungeonId = $this->getCurrentPlayer()->getHuntingDungeonId();
        if ($huntingDungeonId === null) {
            return null;
        }

        return $this->dungeonRepository->findById($huntingDungeonId);
    }
}
