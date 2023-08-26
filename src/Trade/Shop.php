<?php
declare(strict_types=1);

namespace Game\Trade;

use Game\Engine\DBConnection;
use Game\Item\Item;
use Game\Item\ItemPrototypeRepository;

readonly class Shop
{
    public function __construct(
        private int $id,
        public string $name,
        public string $description
    ) {

    }

    /**
     * @return iterable<Offer>
     */
    public function listStock(): iterable
    {
        yield from \DI::getService(StockRepository::class)->listShopStock($this->id);
    }

    public function findOffer(int $itemId): ?Offer
    {
        return \DI::getService(StockRepository::class)->findByIdInShop($itemId, $this->id);
    }
}
