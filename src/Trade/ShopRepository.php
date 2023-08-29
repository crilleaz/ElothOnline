<?php
declare(strict_types=1);

namespace Game\Trade;

use Game\Utils\AbstractDataAccessor;

class ShopRepository extends AbstractDataAccessor
{
    public function findShopByName(string $name): ?Shop
    {
        foreach ($this->listShops() as $shop) {
            if ($shop->name === $name) {
                return $shop;
            }
        }

        return null;
    }

    /**
     * @return iterable<Shop>
     */
    public function listShops(): iterable
    {
        foreach ($this->getData() as $shop) {
            yield new Shop($shop['id'], $shop['name'], $shop['description']);
        }
    }

    protected function  getDataName(): string
    {
        return 'shop';
    }
}
