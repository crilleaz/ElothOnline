<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Client;
use Game\Dungeon\DungeonRepository;
use Game\Player\Activity\Activity as ActivityModel;
use Game\Player\Activity\ActivityRepository;
use Game\Player\Player;
use Game\UI\Scene\Input\InputInterface;
use Game\Utils\TimeInterval;
use Twig\Environment;

class Activity extends AbstractScene
{
    public function __construct(
        Client $client,
        Environment $renderer,
        DungeonRepository $dungeonRepository,
        private readonly ActivityRepository $activityOptionRepository
    ) {
        parent::__construct($client, $renderer, $dungeonRepository);
    }

    public function run(InputInterface $input): string
    {
        $selectedActivity = $input->getString('activity');

        // Player can not perform this activity
        $player = $this->getCurrentPlayer();
        if (!$player->canPerformActivity($selectedActivity)) {
            return $this->switchToScene(MainMenu::class);
        }
        $errorMsg = '';
        $infoMsg  = '';

        $this->handleInput($input, $player);

        $currentActivity = $player->getCurrentActivity();
        $options         = [];
        foreach ($this->activityOptionRepository->getActivityOptions($selectedActivity) as $opt) {
            $option = [
                'id'          => $opt->id,
                'name'        => $opt->name,
                'gainPerHour' => null,
            ];

            $activity            = new ActivityModel($selectedActivity, $opt->id);
            $option['isCurrent'] = $currentActivity !== null && $activity->isSame($currentActivity);
            $reward              = $activity->calculateReward($player, TimeInterval::fromHours(1));
            if (!$reward->isEmpty()) {
                $option['gainPerHour'] = [
                    'exp'  => $reward->exp,
                    'drop' => $reward->items[0],
                ];
            }
            $options[] = $option;
        }

        return $this->renderTemplate('activity', [
            'options'      => $options,
            'errorMsg'     => $errorMsg,
            'infoMsg'      => $infoMsg,
            'activityName' => $selectedActivity,
        ]);
    }

    private function handleInput(InputInterface $input, Player $player): void
    {
        if ($input->getString('action') === 'stop') {
            $player->stopActivity();

            return;
        }

        $selectedActivity = $input->getString('activity');
        $selectedOption   = $input->getInt('option');
        if ($selectedOption > 0) {
            $activity = $this->activityOptionRepository->findActivity($selectedActivity, $selectedOption);
            if ($activity !== null) {
                $player->startActivity($activity);
                return;
            }
        }
    }
}
