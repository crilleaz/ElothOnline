<?php
declare(strict_types=1);

namespace Game\Player;

use Game\Utils\AbstractDataAccessor;

class RaceRepository extends AbstractDataAccessor
{
    public function listAll(): iterable
    {
        foreach ($this->getData() as $race) {
            yield new Race(
                $race['id'],
                $race['name'],
                $race['description'],
                new Stats(
                    $race['stats']['maxHealth'],
                    $race['stats']['strength'],
                    $race['stats']['defence'],
                ),
                new Perks(
                    $race['canCraft'],
                    $race['canMine'],
                    $race['canWoodcut'],
                    $race['canHarvest'],
                    $race['canGather'],
                    $race['canBrew'],
                )
            );
        }
    }

    public function getById(int $raceId): Race
    {
        foreach ($this->listAll() as $race) {
            if ($race->id === $raceId) {
                return $race;
            }
        }

        throw new \RuntimeException('Race does not exist');
    }

    protected function getDataName(): string
    {
        return 'race';
    }
}
