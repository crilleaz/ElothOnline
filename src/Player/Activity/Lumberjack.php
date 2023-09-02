<?php
declare(strict_types=1);

namespace Game\Player\Activity;

use Game\Dungeon\Drop;
use Game\Item\ItemPrototypeRepository;
use Game\Player\Player;
use Game\Player\Reward;

readonly class Lumberjack implements ActivityInterface
{
    public const OPTIONS = [
        1 => [
            'id' => 1,
            'name' => 'Hollow tree',
            'description' => 'Hollow tree. Can be sold as fuel for a coin or two.',
            'complexity' => 1,
            'rewardExp' => 5,
            'rewardItem' => 6,
        ],
        2 => [
            'id' => 2,
            'name' => 'Oak tree',
            'description' => 'Strong tree. They say hogs love hanging around it.',
            'complexity' => 3,
            'rewardExp' => 8,
            'rewardItem' => 7,
        ],
    ];

    private int $optionId;
    private string $optionName;

    public function __construct(int $option)
    {
        if (!isset(self::OPTIONS[$option])) {
            throw new \RuntimeException('Unknown option passed');
        }

        $this->optionId = $option;
        $this->optionName = self::OPTIONS[$option]['name'];
    }

    public function getName(): string
    {
        return 'Lumberjack';
    }

    public function getOption(): int
    {
        return $this->optionId;
    }

    public function getOptionName(): string
    {
        return $this->optionName;
    }

    public function calculateReward(Player $for): Reward
    {
        $playerGeneralEfficiency = $for->getWoodcutting();
        if ($playerGeneralEfficiency === 0) {
            return Reward::none();
        }

        $option = self::OPTIONS[$this->optionId];
        $efficiency = (int)round($playerGeneralEfficiency/$option['complexity']);
        if ($efficiency === 0){
            return Reward::none();
        }

        $itemRepository = \DI::getService(ItemPrototypeRepository::class);

        return new Reward($option['rewardExp'], [new Drop($itemRepository->getById($option['rewardItem']), $efficiency)]);
    }

    public function isSame(ActivityInterface $activity): bool
    {
        return $activity->getName() === $this->getName() && $activity->getOptionName() === $this->getOptionName();
    }
}
