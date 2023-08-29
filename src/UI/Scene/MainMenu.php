<?php
declare(strict_types=1);

namespace Game\UI\Scene;

class MainMenu extends AbstractScene
{
    public function run(): string
    {
        return $this->renderTemplate('main-menu');
    }
}
