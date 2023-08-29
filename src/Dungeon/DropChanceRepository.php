<?php
declare(strict_types=1);

namespace Game\Dungeon;

use Game\Item\ItemPrototypeRepository;
use Game\Utils\Chance;
use Game\Utils\AbstractDataAccessor;

class DropChanceRepository extends AbstractDataAccessor
{
    public function __construct(private readonly ItemPrototypeRepository $itemPrototypeRepository)
    {
    }

    /**
     * @param Monster $monster
     *
     * @return iterable<DropChance>
     */
    public function getMonsterDrop(Monster $monster): iterable
    {
        $data = $this->getData();

        foreach ($data as $dropDetails) {
            if ($dropDetails['monster_id'] == $monster->id) {
                yield new DropChance(
                    Chance::percentage((float)$dropDetails['chance']),
                    $this->itemPrototypeRepository->getById($dropDetails['item_id']),
                    $dropDetails['quantity_min'],
                    $dropDetails['quantity_max']
                );
            }
        }
    }

    protected function getDataName(): string
    {
        return 'droplist';
    }
}
