<?php
declare(strict_types=1);

namespace Game\Player\Activity;

use Game\Player\Player;
use Game\Player\Reward;

interface ActivityInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the option identifier which basically means chopping OAK, or minining IRON
     *
     * @return int
     */
    public function getOption(): int;

    public function getOptionName(): string;

    public function calculateReward(Player $for): Reward;

    public function isSame(ActivityInterface $activity): bool;
}
