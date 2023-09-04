<?php

declare(strict_types=1);

namespace Game\Player;

use Game\Dungeon\Dungeon;
use Game\Dungeon\TTKCalculator;
use Game\Engine\DBConnection;
use Game\Engine\DbTimeFactory;
use Game\Engine\Error;
use Game\Item\Item;
use Game\Item\ItemPrototype as ItemPrototype;
use Game\Player\Activity\Activity;
use Game\Player\Activity\ActivityInterface;
use Game\Skill\Effect\EffectApplier;
use Game\Trade\Offer;

class Player
{
    public const MAX_POSSIBLE_STAMINA = 100;

    private readonly PlayerLog $logger;

    /**
     * @internal Should not be used outside the current module
     */
    public function __construct(
        private readonly int $id,
        private readonly DBConnection $connection
    ) {
        $this->logger = new PlayerLog($this->connection);
    }

    public function isFighting(): bool
    {
        return 1 === (int) $this->getProperty('in_combat');
    }

    public function isInProtectiveZone(): bool
    {
        return !$this->isFighting();
    }

    public function isInDungeon(Dungeon $dungeon): bool
    {
        return $this->getHuntingDungeonId() === $dungeon->id;
    }

    public function getHuntingDungeonId(): ?int
    {
        $hunt = $this->connection->fetchRow('SELECT dungeon_id FROM hunting WHERE character_id = ?', [$this->id]);
        if ($hunt === []) {
            return null;
        }

        return $hunt['dungeon_id'];
    }

    public function enterDungeon(Dungeon $dungeon): null|Error
    {
        $currentDungeonId = $this->getHuntingDungeonId();
        if ($currentDungeonId !== null) {
            if ($currentDungeonId === $dungeon->id) {
                return null;
            }

            return new Error('You are already hunting in a dungeon');
        }

        $now = DbTimeFactory::createCurrentTimestamp();
        $this->connection->execute('INSERT INTO hunting (character_id, dungeon_id, checked_at, last_reward_at) VALUES (?, ?, ?, ?)', [$this->id, $dungeon->id, $now, $now]);
        $this->connection->execute('UPDATE players SET in_combat = 1 WHERE id = ?', [$this->id]);

        return null;
    }

    public function measureDifficulty(Dungeon $dungeon): string
    {
        $ttkCalculator   = new TTKCalculator();
        $ttkMonster      = $ttkCalculator->calculate($this, $dungeon->inhabitant)->seconds;
        $ttkPlayer       = $ttkCalculator->calculateForMonster($dungeon->inhabitant, $this)->seconds;
        $difficultyRatio = $ttkPlayer / $ttkMonster;

        switch (true) {
            case $difficultyRatio > 50:
                return 'easy(>50 mobs/h)';
            case $difficultyRatio > 20:
                return 'moderate(<50 mobs/h)';
            case $difficultyRatio > 1:
                return 'hard(<20 mobs/h)';
            default:
                return 'impossible(0 mobs/h)';
        }
    }

    public function leaveDungeon(): void
    {
        $this->connection->execute('DELETE from hunting WHERE character_id = ?', [$this->id]);
        $this->connection->execute('UPDATE players SET in_combat = 0  WHERE id = ?', [$this->id]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return (string) $this->getProperty('name');
    }

    public function getExp(): int
    {
        return (int) $this->getProperty('experience');
    }

    public function getNextLevelExp(): int
    {
        $nextLvl = $this->getLevel() + 1;

        return LvlCalculator::minExpRequired($nextLvl);
    }

    public function addExp(int $amount): void
    {
        $this->connection->transaction(function (DBConnection $db) use ($amount) {
            $db->execute("UPDATE players SET experience = experience + $amount WHERE id = $this->id");

            $level = LvlCalculator::convertExpToLvl($this->getExp());

            // Update max level
            $db->execute("UPDATE players SET level = {$level} WHERE id = {$this->id}");

            // Update max hp
            $amountToAdd = 15;
            $maxHealth   = $level * $amountToAdd;
            $db->execute("UPDATE players SET health_max = {$maxHealth} WHERE id = {$this->id}");
        });

        $this->logger->add($this->id, "You gained $amount experience points.");
    }

    public function canPerformActivity(string $activity): bool
    {
        return $this->getActivitySkillLevel($activity) > 0;
    }

    public function getActivitySkillLevel(string $activity): int
    {
        switch ($activity) {
            case Activity::NAME_LUMBERJACK:
                return $this->getWoodcutting();
            case Activity::NAME_FARMER:
                return $this->getHarvesting();
            case Activity::NAME_MINER:
                return $this->getMining();
            case Activity::NAME_GATHERER:
                return $this->getGathering();
            case Activity::NAME_CRAFTER:
                return $this->getBlacksmith();
            case Activity::NAME_ALCHEMIST:
                return $this->getAlchemy();
            default:
                return 0;
        }
    }

    public function startActivity(ActivityInterface $activity): ?Error
    {
        if (!$this->isInProtectiveZone()) {
            return new Error('Can not go for activities when not in protective zone');
        }

        if ($this->getCurrentActivity() !== null) {
            return new Error('Character is busy with another activity');
        }

        $now = DbTimeFactory::createCurrentTimestamp();

        $this->connection->execute('
                        INSERT INTO activity(character_id, name, selected_option, started_at, checked_at, last_reward_at)
                        VALUE (?, ?, ?, ?, ?, ?)
        ', [$this->id, $activity->getName(), $activity->getOption(), $now, $now, $now]);

        return null;
    }

    public function getCurrentActivity(): ?CharacterActivity
    {
        $data = $this->connection->fetchRow('SELECT name, selected_option, checked_at, last_reward_at FROM activity WHERE character_id=' . $this->id);
        if ($data === []) {
            return null;
        }

        return new CharacterActivity(
            $data['name'],
            $data['selected_option'],
            DbTimeFactory::fromTimestamp($data['checked_at']),
            DbTimeFactory::fromTimestamp($data['last_reward_at'])
        );
    }

    public function stopActivity(): void
    {
        $this->connection->execute('DELETE FROM activity WHERE character_id=' . $this->id);
    }

    public function getLevel(): int
    {
        return (int) $this->getProperty('level');
    }

    public function getGold(): int
    {
        return $this->getItemQuantity(1);
    }

    public function getCrystals(): int
    {
        // TODO likely has to be similar to gold and reside in items
        return (int) $this->getProperty('crystals');
    }

    public function getStamina(): int
    {
        return (int) $this->getProperty('stamina');
    }

    public function restoreStamina(int $amount): void
    {
        $this->connection->execute('UPDATE players SET stamina = LEAST(stamina + ?, ?) WHERE id=?', [$amount, self::MAX_POSSIBLE_STAMINA, $this->id]);
    }

    public function getMaxHealth(): int
    {
        return (int) $this->getProperty('health_max');
    }

    public function getCurrentHealth(): int
    {
        return (int) $this->getProperty('health');
    }

    public function getMagic(): int
    {
        return (int) $this->getProperty('magic');
    }

    public function getStrength(): int
    {
        return (int) $this->getProperty('strength');
    }

    public function getDefence(): int
    {
        return (int) $this->getProperty('defence');
    }

    public function getWoodcutting(): int
    {
        return (int) $this->getProperty('woodcutting');
    }

    public function getMining(): int
    {
        return (int) $this->getProperty('mining');
    }

    public function getGathering(): int
    {
        return (int) $this->getProperty('gathering');
    }

    public function getHarvesting(): int
    {
        return (int) $this->getProperty('harvesting');
    }

    public function getAlchemy(): int
    {
        return (int) $this->getProperty('alchemy');
    }

    public function getBlacksmith(): int
    {
        return (int) $this->getProperty('blacksmith');
    }

    /**
     * @return iterable<string>
     */
    public function getLogs(int $amount): iterable
    {
        return $this->logger->readLogs($this->id, $amount);
    }

    public function pickUp(Item $item): void
    {
        $this->obtainItem($item);

        $this->logger->add($this->id, sprintf('You picked up %d %s', $item->quantity, $item->name));
    }

    public function obtainItem(Item $item): void
    {
        if ($this->getItemQuantity($item->id) === 0) {
            $this->connection
                ->execute('INSERT INTO inventory (character_id, item_id, amount, worth) VALUES (?, ?, ?, ?)', [$this->id, $item->id, $item->quantity, $item->worth]);
        } else {
            $this->connection
                ->execute('UPDATE inventory SET amount = amount + ? WHERE item_id = ? AND character_id = ?', [$item->quantity, $item->id, $this->id]);
        }
    }

    public function dropItem(ItemPrototype $item, int $quantity): Item
    {
        $this->connection->transaction(function () use ($item, $quantity) {
            $this->connection->execute('UPDATE inventory SET amount = amount - ? WHERE item_id = ? AND character_id = ?', [$quantity, $item->id, $this->id]);

            $remainingItemsQuantity = $this->getItemQuantity($item->id);
            if ($remainingItemsQuantity < 0) {
                throw new \RuntimeException('Player does not have that many items');
            }
            $this->removeNonExistentItems();
        });

        return new Item($item->id, $quantity);
    }

    public function destroyItem(ItemPrototype $item, int $quantity = null): void
    {
        if ($quantity === null) {
            $this->connection->execute('DELETE FROM inventory WHERE item_id = ? AND character_id = ?', [$item->id, $this->id]);

            return;
        }

        if ($quantity < 1) {
            throw new \DomainException('Can not destroy 0 or less items');
        }

        $this->connection->execute('UPDATE inventory SET amount=GREATEST(amount-?, 0) WHERE item_id = ? AND character_id = ?', [$quantity, $item->id, $this->id]);

        $this->removeNonExistentItems();
    }

    public function useItem(int $itemId): ?Error
    {
        $item = $this->findInInventory($itemId);
        if ($item === null) {
            return new Error('Player does not have such item');
        }

        $this->connection->transaction(function () use ($item) {
            foreach ($item->listEffects() as $effect) {
                $error = EffectApplier::apply($effect, $this);
                if ($error !== null) {
                    throw new \RuntimeException($error->message);
                }
            }

            if ($item->isConsumable()) {
                $this->destroyItem($item->prototype, 1);
            }
        });

        return null;
    }

    /**
     * @return iterable<Item>
     */
    public function getInventory(): iterable
    {
        $entries = $this->connection->fetchRows('SELECT item_id, amount FROM inventory WHERE character_id = ?', [$this->id]);
        foreach ($entries as $entry) {
            yield new Item($entry['item_id'], $entry['amount']);
        }
    }

    public function findInInventory(int $itemId): ?Item
    {
        $item = $this->connection->fetchRow('SELECT item_id, amount FROM inventory WHERE character_id = ? AND item_id=?', [$this->id, $itemId]);

        if ($item === []) {
            return null;
        }

        return new Item($item['item_id'], $item['amount']);
    }

    public function canAfford(Offer $offer): bool
    {
        $requiredQuantity = $offer->inExchange->quantity;
        $existingQuantity = $this->getItemQuantity($offer->inExchange->id);

        return $existingQuantity >= $requiredQuantity;
    }

    public function acceptOffer(Offer $offer): ?Error
    {
        if (!$this->canAfford($offer)) {
            return new Error('Player does not have enough items to fulfil the offer');
        }

        $this->connection->transaction(function () use ($offer) {
            // TODO drop returns actually dropped item which means that it can be used for actual trade player<=>seller
            $this->dropItem($offer->inExchange->prototype, $offer->inExchange->quantity);
            $this->obtainItem($offer->item);
        });

        return null;
    }

    private function getProperty(string $property): string|int|float|null
    {
        $result = $this->connection->fetchRow("SELECT {$property} FROM players WHERE id = ?", [$this->id]);
        if ($result === []) {
            throw new \RuntimeException('Player does not exist');
        }

        return $result[$property];
    }

    private function getItemQuantity(int $itemId): int
    {
        $result = $this->connection->fetchRow("SELECT amount FROM inventory WHERE item_id = $itemId AND character_id = ?", [$this->id]);
        if ($result === []) {
            return 0;
        }

        return (int) $result['amount'];
    }

    private function removeNonExistentItems(): void
    {
        $this->connection->execute('DELETE FROM inventory WHERE character_id=? AND amount=0', [$this->id]);
    }
}
