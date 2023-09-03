<?php

declare(strict_types=1);

namespace Game\UI\Scene\Activity;

use Game\Player\Player;
use Game\UI\Scene\AbstractScene;
use Game\UI\Scene\Input\InputInterface;
use Game\UI\Scene\MainMenu;
use Game\Utils\TimeInterval;

class Lumberjack extends AbstractScene
{
    public function run(InputInterface $input): string
    {
        $player = $this->getCurrentPlayer();
        // Player can not perform this activity
        if ($player->getWoodcutting() < 1) {
            return $this->switchToScene(MainMenu::class);
        }
        $errorMsg = '';
        $infoMsg  = '';

        $this->handleInput($input, $player);

        $currentActivity = $player->getCurrentActivity();
        $options         = [];
        foreach (\Game\Player\Activity\Lumberjack::OPTIONS as $option) {
            $activity            = new \Game\Player\Activity\Lumberjack($option['id']);
            $option['isCurrent'] = $currentActivity !== null && $activity->isSame($currentActivity);
            $reward              = $activity->calculateReward($player, TimeInterval::fromHours(1));
            if ($reward->isEmpty()) {
                $option['gainPerHour'] = null;
            } else {
                $option['gainPerHour'] = [
                    'exp'  => $reward->exp,
                    'drop' => $reward->items[0],
                ];
            }
            $options[] = $option;
        }

        return $this->renderTemplate('activity/lumberjack', [
            'options'  => $options,
            'errorMsg' => $errorMsg,
            'infoMsg'  => $infoMsg,
        ]);
    }

    private function handleInput(InputInterface $input, Player $player): void
    {
        if ($input->getString('action') === 'stop') {
            $player->stopActivity();

            return;
        }

        $selectedOption = $input->getInt('tree');
        if ($selectedOption > 0) {
            foreach (\Game\Player\Activity\Lumberjack::OPTIONS as $option) {
                // If option exists then start activity
                if ($selectedOption === $option['id']) {
                    $player->startActivity(new \Game\Player\Activity\Lumberjack($option['id']));
                    return;
                }
            }
        }
    }
}
