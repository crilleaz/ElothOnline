<?php
declare(strict_types=1);

namespace Game\UI\Scene;

class MainMenu extends AbstractScene
{
    public function run(): string
    {
        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('main-menu', [
            'player' => $player,
        ]);
    }
}
