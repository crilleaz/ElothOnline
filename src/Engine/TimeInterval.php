<?php
declare(strict_types=1);

namespace Game\Engine;

class TimeInterval
{
    public static function fromMinutes(int $minutes): self
    {
        return new self($minutes * 60);
    }

    public function __construct(private readonly int $seconds)
    {
        if ($seconds < 0) {
            throw new \RuntimeException('Interval can not be negative');
        }
    }

    public function toMinutes(): float
    {
        return $this->seconds/60;
    }
}