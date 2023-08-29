<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Utils\AbstractDataAccessor;

class MonsterRepository extends AbstractDataAccessor
{
    public function getById(int $id): Monster
    {
        foreach ($this->listMonsters() as $monster) {
            if ($monster->id === $id) {
                return $monster;
            }
        }

        throw new \DomainException('Monster does not exist :)');
    }

    /**
     * @return iterable<Monster>
     */
    public function listMonsters(): iterable
    {
        foreach ($this->getData() as $monster) {
            yield new Monster(
                $monster['id'],
                $monster['name'],
                $monster['health'],
                $monster['experience'],
                $monster['attack'],
                $monster['defence']
            );
        }
    }

    protected function getDataName(): string
    {
        return 'monster';
    }
}
