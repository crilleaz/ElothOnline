<?php

declare(strict_types=1);

namespace Game\Player;

use Carbon\CarbonImmutable;
use Game\Player\Activity\Activity;
use Game\Player\Activity\ActivityInterface;
use Game\Utils\TimeInterval;

readonly class CharacterActivity implements ActivityInterface
{
    private ActivityInterface $activity;

    public function __construct(
        string $name,
        int $option,
        public CarbonImmutable $checkedAt,
        public CarbonImmutable $rewardedAt
    ) {
        $this->activity = new Activity($name, $option);
    }

    public function getName(): string
    {
        return $this->activity->getName();
    }

    public function getOption(): int
    {
        return $this->activity->getOption();
    }

    public function getOptionName(): string
    {
        return $this->activity->getOptionName();
    }

    public function calculateReward(Player $for, TimeInterval $duration): Reward
    {
        return $this->activity->calculateReward($for, $duration);
    }

    public function isSame(ActivityInterface $activity): bool
    {
        return $this->activity->isSame($activity);
    }
}
