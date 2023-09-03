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
    /**
     * @param int $shopId
     * @return iterable<Offer>
     */
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

    /**
     * @return array<int, array{shop_id: int, item_id: int, price_id: int, price_quantity: int}[]>
     */
    protected function getData(): array
    {
        $data = [];
        /** @var array{shop_id: int, item_id: int, price_id: int, price_quantity: int} $entry */
        foreach(parent::getData() as $entry) {
            $data[$entry['shop_id']][] = $entry;
        }

        return $data;
    }
}
