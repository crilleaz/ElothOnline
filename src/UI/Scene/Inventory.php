<?php
declare(strict_types=1);

namespace Game\UI\Scene;

class Inventory extends AbstractScene
{
    public function run(): string
    {
        $player = $this->game->getCurrentPlayer();

        return $this->renderTemplate('inventory', [
            'player' => $player,
        ]);
    }
}
