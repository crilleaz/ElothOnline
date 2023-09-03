<?php

declare(strict_types=1);

namespace Game\Utils;

use DateTimeInterface;

readonly class TimeInterval
{
    public static function fromMinutes(int $minutes): self
    {
        return new self($minutes * 60);
    }

    public static function fromHours(int $hours): self
    {
        return self::fromMinutes($hours * 60);
    }

    public static function between(DateTimeInterface $from, DateTimeInterface $to): self
    {
        return new self($to->getTimestamp() - $from->getTimestamp());
    }

    public function __construct(public int $seconds)
    {
        if ($seconds < 0) {
            throw new \RuntimeException('Interval can not be negative');
        }
    }

    public function toMinutes(): float
    {
        return $this->seconds / 60;
    }

    public function toHours(): float
    {
        return $this->toMinutes() / 60;
    }

    public function isGreaterThan(self $interval): bool
    {
        return $this->seconds > $interval->seconds;
    }
}
