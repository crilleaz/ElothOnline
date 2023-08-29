<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\UI\Scene\Input\InputInterface;

class Highscore extends AbstractScene
{
    public function run(InputInterface $input): string
    {
        $player = $this->getCurrentPlayer();
        $topPlayers = $this->client->listTopPlayers(100);

        return $this->renderTemplate('highscores', [
            'player' => $player,
            'topPlayers' => $topPlayers,
        ]);
    }
}
