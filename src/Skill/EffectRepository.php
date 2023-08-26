<?php
declare(strict_types=1);

namespace Game\Skill;

use Game\Skill\Effect\EffectType;
use Game\Utils\AbstractDataAccessor;

class EffectRepository extends AbstractDataAccessor
{
    public function findByItem(int $itemId): iterable
    {
        foreach ($this->getData() as $effect) {
            if ($effect['item_id'] === $itemId) {
                yield new Effect($effect['name'], EffectType::from($effect['type']), $effect['power']);
            }
        }
    }

    protected function getDataName(): string
    {
        return 'item_effect';
    }
}
