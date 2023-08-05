<?php
declare(strict_types=1);

namespace Game;

readonly class Error
{
    public function __construct(public string $message) {}
}
