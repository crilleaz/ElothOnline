<?php

declare(strict_types=1);

namespace Game\Player;

use Carbon\CarbonImmutable;
use Game\Player\Activity\ActivityInterface;
use Game\Player\Activity\Lumberjack;
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
        $this->activity = $this->resolveActivity($name, $option);
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

    private function resolveActivity(string $name, int $option): ActivityInterface
    {
        switch ($name) {
            case 'Lumberjack':
                return new Lumberjack($option);
            default:
                throw new \RuntimeException('Unknown activity');
        }
    }
}
