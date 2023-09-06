<?php

declare(strict_types=1);

namespace Game\Player\Activity;

use Game\Player\Player;
use Game\Player\Reward;
use Game\Utils\TimeInterval;

interface ActivityInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the option identifier which basically means chopping OAK, or mining IRON
     *
     * @return int
     */
    public function getOption(): int;

    public function getOptionName(): string;

    /**
     * @TODO feels as if it is in the wrong place. Maybe Player is the right one.
     */
    public function calculateReward(Player $for, TimeInterval $duration): Reward;

    public function isSame(ActivityInterface $activity): bool;
}
