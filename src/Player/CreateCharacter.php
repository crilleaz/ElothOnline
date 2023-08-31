<?php
declare(strict_types=1);

namespace Game\Player;

use Game\Chat\Chat;
use Game\Engine\DBConnection;
use Game\Item\ItemPrototypeRepository;
use Game\User;

readonly class CreateCharacter
{
    public function __construct(
        private DBConnection $db,
        private CharacterRepository $characterRepository,
        private ItemPrototypeRepository $itemPrototypeRepository,
        private Chat $chat
    ) {}

    public function execute(string $characterName, Race $race, User $forUser): Player
    {
        $this->db->execute(
            "INSERT INTO players(user_id, race, name, health, health_max, strength, defence) VALUE (?, ?, ?, ?, ?, ?, ?)",
            [
                $forUser->id,
                $race->id,
                $characterName,
                $race->stats->maxHealth,
                $race->stats->maxHealth,
                $race->stats->strength,
                $race->stats->defence
            ]
        );

        $gold = $this->itemPrototypeRepository->getById(1);

        $character = $this->characterRepository->getByUser($forUser);

        $character->obtainItem($gold, 10);
        $this->chat->addSystemMessage(sprintf('New character %s appears in the world', $characterName));

        return $character;
    }
}
