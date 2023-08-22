<?php
declare(strict_types=1);

namespace Game\Trade;

use Game\Engine\DBConnection;
use Game\Item\Item;
use Game\Item\ItemPrototypeRepository;

readonly class Shop
{
    // TODO remove itemPrototypeRepository and replace with joins
    public function __construct(
        private int $id,
        public string $name,
        public string $description,
        private DBConnection $db,
        private ItemPrototypeRepository $itemPrototypeRepository
    )
    {

    }

    /**
     * @return iterable<Offer>
     */
    public function listStock(): iterable
    {
        foreach ($this->db->fetchRows('SELECT * FROM shop_stock WHERE shop_id=' . $this->id) as $stockEntry) {
            yield new Offer(
                new Item($this->itemPrototypeRepository->getById($stockEntry['item_id']), 1),
                new Item($this->itemPrototypeRepository->getById($stockEntry['price_id']), $stockEntry['price_quantity']),
            );
        }
    }
}
