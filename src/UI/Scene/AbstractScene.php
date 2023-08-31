<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Dungeon\Dungeon;
use Game\Dungeon\DungeonRepository;
use Game\Client;
use Game\Player\Player;
use Game\UI\Scene\Input\HttpInput;
use Twig\Environment;

abstract class AbstractScene implements SceneInterface
{
    public function __construct(
        protected readonly Client          $client,
        private readonly Environment       $renderer,
        private readonly DungeonRepository $dungeonRepository
    ) {

    }

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
     * @param class-string<SceneInterface> $sceneName
     * @return string
     */
    protected function switchToScene(string $sceneName): string
    {
        /** @var SceneInterface $scene */
        $scene =\DI::getService($sceneName);

        return $scene->run(new HttpInput());
    }

    protected function getCurrentPlayer(): Player
    {
        return $this->client->getCurrentPlayer();
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
