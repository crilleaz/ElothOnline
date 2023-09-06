<?php

declare(strict_types=1);

namespace Game\Utils;

readonly class Chance
{
    private int $precision;

    private float $value;

    private function __construct(float $rate)
    {
        $this->precision = 3;

        $rate = round($rate, $this->precision);

        if ($rate > 1.0) {
            $rate = 1.0;
        }

        if ($rate < 0.0) {
            throw new \InvalidArgumentException('Probability can not be negative');
        }

        $this->value = $rate;
    }

    public static function raw(float $rate): self
    {
        return new self($rate);
    }

    public static function percentage(float $rate): self
    {
        return new self($rate / 100.0);
    }

    public function roll(): bool
    {
        $maximum = pow(10, $this->precision);

        return mt_rand(0, $maximum) <= $this->value * $maximum;
    }
}
