<?php
declare(strict_types=1);

namespace Game\UI\Scene;

use Game\UI\Scene\Input\InputInterface;

class Inventory extends AbstractScene
{
    public function run(InputInterface $input): string
    {
        return $this->renderTemplate('inventory');
    }
}
