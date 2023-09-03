<?php

declare(strict_types=1);

namespace Game\Player;

use Game\Chat\Chat;
use Game\Engine\DBConnection;
use Game\Item\Item;
use Game\User;

readonly class CreateCharacter
{
    public function __construct(
        private DBConnection $db,
        private CharacterRepository $characterRepository,
        private Chat $chat
    ) {
    }

    public function execute(string $characterName, Race $race, User $forUser): Player
    {
        $this->db->execute(
            'INSERT INTO players(
                    user_id,
                    race,
                    name,
                    health,
                    health_max,
                    strength,
                    defence,
                    woodcutting,
                    harvesting,
                    mining,
                    blacksmith,
                    gathering,
                    alchemy
                ) VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $forUser->id,
                $race->id,
                $characterName,
                $race->stats->maxHealth,
                $race->stats->maxHealth,
                $race->stats->strength,
                $race->stats->defence,
                $race->perks->canWoodcut ? 1 : 0,
                $race->perks->canHarvest ? 1 : 0,
                $race->perks->canMine ? 1 : 0,
                $race->perks->canCraft ? 1 : 0,
                $race->perks->canGather ? 1 : 0,
                $race->perks->canBrew ? 1 : 0,
            ]
        );

        $goldId = 1;

        $character = $this->characterRepository->getByUser($forUser);

        $character->obtainItem(new Item($goldId, 10));
        $this->chat->addSystemMessage(sprintf('New character %s appears in the world', $characterName));

        return $character;
    }
}
