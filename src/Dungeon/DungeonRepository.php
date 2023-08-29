<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Utils\AbstractDataAccessor;

class DungeonRepository extends AbstractDataAccessor
{
    public function __construct(private readonly MonsterRepository $monsterRepository) {}

    public function findById(int $id): ?Dungeon
    {
        foreach ($this->listDungeons() as $dungeon) {
            if ($dungeon->id === $id) {
                return $dungeon;
            }
        }

        return null;
    }

    public function getById(int $id): Dungeon
    {
        $dungeon = $this->findById($id);
        if ($dungeon === null) {
            throw new \RuntimeException('Dungeon does not exist');
        }

        return $dungeon;
    }

    public function listDungeons(): iterable
    {
        foreach ($this->getData() as $dungeon) {
            yield new Dungeon(
                $dungeon['id'],
                $dungeon['name'],
                $dungeon['description'],
                $this->monsterRepository->getById($dungeon['monster_id'])
            );
        }
    }

    protected function getDataName(): string
    {
        return 'dungeon';
    }
}
