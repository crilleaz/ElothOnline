<?php
declare(strict_types=1);

namespace Game\Player;

use Game\Utils\AbstractDataAccessor;

/**
 * @phpstan-type RawData array{
 * id: int,
 * name: string,
 * description: string,
 * stats: array{maxHealth: int, strength: int, defence: int},
 * canCraft: bool,
 * canMine: bool,
 * canWoodcut: bool,
 * canHarvest: bool,
 * canGather: bool,
 * canBrew: bool
 * }
 */
class RaceRepository extends AbstractDataAccessor
{
    /**
     * @return iterable<Race>
     */
    public function listAll(): iterable
    {
        /** @var RawData $race */
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

    /**
     * @param int $raceId
     *
     * @return Race
     */
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
