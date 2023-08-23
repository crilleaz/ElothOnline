<?php
declare(strict_types=1);

namespace Game\UI\Scene;

class Highscore extends AbstractScene
{
    public function run(): string
    {
        $player = $this->game->getCurrentPlayer();
        $topPlayers = $this->game->listTopPlayers(100);

        return $this->renderTemplate('highscores', [
            'player' => $player,
            'topPlayers' => $topPlayers,
        ]);
    }
}
