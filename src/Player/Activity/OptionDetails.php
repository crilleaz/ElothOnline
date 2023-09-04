<?php

declare(strict_types=1);

namespace Game\Player\Activity;

readonly class OptionDetails
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public int $complexity,
        public int $rewardExp,
        public int $rewardItemId
    ) {
    }
}
