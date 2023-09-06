<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\UI\Scene\Input\InputInterface;

interface SceneInterface
{
    public function run(InputInterface $input): string;
}
