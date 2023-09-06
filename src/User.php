<?php

declare(strict_types=1);

namespace Game;

readonly class User
{
    public function __construct(public int $id, public string $name, public bool $isBanned)
    {
    }

    public function isAdmin(): bool
    {
        return self::hasAdminAccess($this->name);
    }

    /**
     * @param  string $userName
     * @return bool
     *
     * @TODO temporary solution. Remove it completely
     */
    public static function hasAdminAccess(string $userName): bool
    {
        return $userName === 'crilleaz' || $userName === 'GM Crille';
    }
}
