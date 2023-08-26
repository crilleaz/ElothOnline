<?php
declare(strict_types=1);

namespace Game\Item;

use Game\Utils\AbstractDataAccessor;

final class ItemPrototypeRepository extends AbstractDataAccessor
{
    private array $cache = [];

    public function getById(int $id): ItemPrototype
    {
        if (!isset($this->cache[$id])) {
            $prototype = [];
            foreach ($this->getData() as $itemData) {
                if ($itemData['id'] === $id) {
                    $prototype = $itemData;
                    break;
                }
            }

            if ($prototype === []) {
                throw new \RuntimeException('Unknown item');
            }

            $this->cache[$id] = new ItemPrototype($id, $prototype['name'], (int) $prototype['worth']);
        }

        return $this->cache[$id];
    }

    protected function getDataName(): string
    {
        return 'item';
    }
}
