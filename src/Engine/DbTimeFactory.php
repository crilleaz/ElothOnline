<?php
declare(strict_types=1);

namespace Game\Engine;

use Carbon\CarbonImmutable;
use DateTimeInterface;

class DbTimeFactory
{
    public static function createCurrentTimestamp(): string
    {
        return self::createTimestamp(CarbonImmutable::now());
    }

    public static function createTimestamp(DateTimeInterface $from): string
    {
        return $from->format('Y-m-d H:i:s');
    }
}
