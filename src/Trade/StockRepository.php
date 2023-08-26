<?php
declare(strict_types=1);

namespace Game\Trade;

use Game\Item\Item;
use Game\Utils\AbstractDataAccessor;

/**
 * @internal
 */
class StockRepository extends AbstractDataAccessor
{
    public function listShopStock(int $shopId): iterable
    {
        $stock = $this->getData()[$shopId] ?? [];

        foreach ($stock as $entry) {
            yield new Offer(
                new Item($entry['item_id'], 1),
                new Item($entry['price_id'], $entry['price_quantity']),
            );
        }
    }

    public function findByIdInShop(int $itemId, int $shopId): ?Offer
    {
        foreach ($this->listShopStock($shopId) as $shopOffer) {
            if ($shopOffer->item->id === $itemId) {
                return $shopOffer;
            }
        }

        return null;
    }

    protected function getDataName(): string
    {
        return 'shop_stock';
    }

    protected function getData(): array
    {
        $data = [];
        foreach(parent::getData() as $entry) {
            $data[$entry['shop_id']][] = $entry;
        }

        return $data;
    }
}
