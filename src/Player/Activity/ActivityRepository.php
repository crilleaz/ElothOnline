<?php

declare(strict_types=1);

namespace Game\Player\Activity;

use Game\Utils\AbstractDataAccessor;

class ActivityRepository extends AbstractDataAccessor
{
    /**
     * @TODO think about encapsulation. Maybe generic-solution interface is a bad idea as a long-term one.
     *
     * @return OptionDetails[]
     */
    public function getActivityOptions(string $activityName): array
    {
        $raw = (array) ($this->getData()[$activityName] ?? []);

        $entries = [];
        /** @var array{id: int, name: string, description:string, complexity: int, reward_exp: int, reward_item_id: int} $entry */
        foreach ($raw as $entry) {
            $entries[] = new OptionDetails(
                $entry['id'],
                $entry['name'],
                $entry['description'],
                $entry['complexity'],
                $entry['reward_exp'],
                $entry['reward_item_id']
            );
        }

        return $entries;
    }

    public function findActivity(string $activity, int $optionId): ?Activity
    {
        $option = $this->findActivityOption($activity, $optionId);
        if ($option === null) {
            return null;
        }

        return new Activity($activity, $optionId);
    }

    public function findActivityOption(string $activity, int $optionId): ?OptionDetails
    {
        foreach ($this->getActivityOptions($activity) as $option) {
            if ($option->id === $optionId) {
                return $option;
            }
        }

        return null;
    }

    protected function getDataName(): string
    {
        return 'activity_option';
    }
}
