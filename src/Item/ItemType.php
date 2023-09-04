<?php

declare(strict_types=1);

namespace Game\Item;

enum ItemType: string
{
    case CURRENCY   = 'currency';
    case CONSUMABLE = 'consumable';
    case WEAPON     = 'weapon';
    case ARMOR      = 'armor';
    case MATERIAL   = 'material';
}
