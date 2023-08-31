<?php
declare(strict_types=1);

namespace Game\UI\Scene\Input;

class HttpInput implements InputInterface
{
    public function __construct()
    {

    }

    public function getInt(string $input): int
    {
        $value = $_POST[$input] ?? $_GET[$input] ?? 0;
        if (!is_numeric($value)) {
            return 0;
        }

        return (int) $value;
    }

    public function getString(string $input): string
    {
        $value = $_POST[$input] ?? $_GET[$input] ?? '';
        if (!is_string($value)) {
            return '';
        }

        return $value;
    }
}
