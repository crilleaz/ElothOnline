<?php
declare(strict_types=1);

namespace Game\UI\Scene\Input;

interface InputInterface
{
    public function getInt(string $input): int;

    public function getString(string $input): string;
}
