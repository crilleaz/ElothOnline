<?php
declare(strict_types=1);

namespace Game;

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testNothing(): void
    {
        self::assertEquals('123', '123');
    }
}
