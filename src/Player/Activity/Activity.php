<?php

declare(strict_types=1);

namespace Game\Player\Activity;

use Game\Item\Item;
use Game\Player\Player;
use Game\Player\Reward;
use Game\Utils\TimeInterval;

readonly class Activity implements ActivityInterface
{
    public const NAME_LUMBERJACK = 'Lumberjack';
    public const NAME_FARMER     = 'Farmer';
    public const NAME_MINER      = 'Miner';
    public const NAME_GATHERER   = 'Gatherer';
    public const NAME_CRAFTER    = 'Crafter';
    public const NAME_ALCHEMIST  = 'Alchemist';

    private OptionDetails $option;
    private string $name;

    public function __construct(string $name, int $option)
    {
        if (!$this->exists($name)) {
            throw new \RuntimeException(sprintf('Unknown activity "%s"', $name));
        }

        $optionDetails = \DI::getService(ActivityRepository::class)->findActivityOption($name, $option);
        if ($optionDetails === null) {
            throw new \RuntimeException(sprintf('Unknown option "%d" for activity "%s"', $option, $name));
        }

        $this->name   = $name;
        $this->option = $optionDetails;
    }

    public static function lumberjack(int $option): self
    {
        return new self(self::NAME_LUMBERJACK, $option);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOption(): int
    {
        return $this->option->id;
    }

    public function getOptionName(): string
    {
        return $this->option->name;
    }

    public function calculateReward(Player $for, TimeInterval $duration): Reward
    {
        if (!$for->canPerformActivity($this->name)) {
            return Reward::none();
        }

        $playerGeneralEfficiency = $for->getActivitySkillLevel($this->name);

        $efficiency = (int) round($playerGeneralEfficiency / $this->option->complexity);
        if ($efficiency === 0) {
            return Reward::none();
        }

        $rewardPerHour = new Reward($this->option->rewardExp, [new Item($this->option->rewardItemId, $efficiency)]);

        return $rewardPerHour->multiply($duration->toHours());
    }

    public function isSame(ActivityInterface $activity): bool
    {
        return $activity->getName() === $this->getName() && $activity->getOptionName() === $this->getOptionName();
    }

    private static function exists(string $name): bool
    {
        return in_array($name, [
            self::NAME_LUMBERJACK,
            self::NAME_FARMER,
        ]);
    }
}
