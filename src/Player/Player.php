<?php
declare(strict_types=1);

namespace Game\Player;

use Game\Dungeon\Drop;
use Game\Dungeon\Dungeon;
use Game\Dungeon\Monster;
use Game\Dungeon\TTKCalculator;
use Game\Engine\DBConnection;
use Game\Engine\DbTimeFactory;
use Game\Engine\Error;
use Game\Item\Item;
use Game\Item\ItemPrototype as ItemPrototype;

class Player
{
    public const MAX_POSSIBLE_STAMINA = 100;

    private readonly PlayerLog $logger;

    public static function loadPlayer(string $name, DBConnection $connection): self
    {
        return new self($name, $connection);
    }

    public static function exists(string $name, DBConnection $connection): bool
    {
        $result = $connection->fetchRow("SELECT name FROM players WHERE name='$name'");

        return isset($result['name']);
    }

    private function __construct(
        private readonly string $name,
        private readonly DBConnection $connection
    )
    {
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
        $huntingDungeon = $this->getHuntingDungeon();
        if ($huntingDungeon === null) {
            return false;
        }

        return $huntingDungeon->id === $dungeon->id;
    }

    public function getHuntingDungeon(): ?Dungeon
    {
        $dungeon = $this->connection->fetchRow('
                        SELECT h.dungeon_id as id, d.name, d.description, m.name as monsterName, m.monster_id as monsterId, m.health, m.attack, m.defence, m.experience
                                 FROM hunting h
                                    INNER JOIN dungeons d ON d.id=h.dungeon_id
                                    INNER JOIN monster m ON d.monster_id = m.monster_id
                                 WHERE h.username = ?
        ',[$this->name]);

        if ($dungeon === []) {
            return null;
        }

        return new Dungeon(
            $dungeon['id'],
            $dungeon['name'],
            $dungeon['description'],
            new Monster($dungeon['monsterId'], $dungeon['monsterName'], $dungeon['health'], $dungeon['experience'], $dungeon['attack'], $dungeon['defence'])
        );
    }

    public function enterDungeon(int $id): null|Error
    {
        $currentDungeon = $this->getHuntingDungeon();
        if ($currentDungeon !== null) {
            if ($currentDungeon->id === $id) {
                return null;
            }

            return new Error('You are already hunting at ' . $currentDungeon->name);
        }

        $dungeon = Dungeon::loadById($id, $this->connection);
        if ($dungeon === null) {
            return new Error(sprintf('Dungeon with id "%d" does not exist', $id));
        }

        $this->connection->execute('INSERT INTO hunting (username, dungeon_id, tid) VALUES (?, ?, ?)', [$this->name, $id, DbTimeFactory::createCurrentTimestamp()]);
        $this->connection->execute('UPDATE players SET in_combat = 1 WHERE name = ?', [$this->name]);

        return null;
    }

    public function measureDifficulty(Dungeon $dungeon): string
    {
        $ttkCalculator = new TTKCalculator();
        $ttkMonster = $ttkCalculator->calculate($this, $dungeon->inhabitant)->seconds;
        $ttkPlayer = $ttkCalculator->calculateForMonster($dungeon->inhabitant, $this)->seconds;
        $difficultyRatio = $ttkPlayer / $ttkMonster;

        switch(true) {
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
        $this->connection->execute('DELETE from hunting WHERE username = ?', [$this->name]);
        $this->connection->execute('UPDATE players SET in_combat = 0  WHERE name = ?', [$this->name]);
    }

    public function isAdmin(): bool
    {
        return $this->name === 'crilleaz' || $this->name === 'GM Crille';
    }

    // TODO likely unused. remove if so
    public function getId(): int
    {
        return (int) $this->getProperty('id');
    }

    public function getName(): string
    {
        return $this->name;
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
            $db->execute("UPDATE players SET experience = experience + $amount WHERE name = '{$this->name}'");

            $level = LvlCalculator::convertExpToLvl($this->getExp());

            // Update max level
            $db->execute("UPDATE players SET level = {$level} WHERE name = '{$this->name}'");

            // Update max hp
            $amountToAdd = 15;
            $maxHealth = $level * $amountToAdd;
            $db->execute("UPDATE players SET health_max = {$maxHealth} WHERE name = '{$this->name}'");
        });

        $this->logger->add($this->name, "You gained $amount experience points.");
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

    public function getHerbalism(): int
    {
        return (int) $this->getProperty('herbalism');
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
        return $this->logger->readLogs($this->name, $amount);
    }

    public function pickUp(Drop $drop): void
    {
        $this->obtain($drop->item, $drop->quantity);
        $this->logger->add($this->name, sprintf("You picked up %d %s", $drop->quantity, $drop->item->name));
    }

    public function obtain(ItemPrototype $item, int $quantity): void
    {
        $entry = $this->connection
            ->fetchRow('SELECT amount FROM inventory WHERE item_id = ? AND username = ?', [$item->id, $this->name]);

        if ($entry === []) {
            $this->connection
                ->execute('INSERT INTO inventory (username, item_id, amount, worth) VALUES (?, ?, ?, ?)', [$this->name, $item->id, $quantity, $item->worth]);
        } else {
            $this->connection
                ->execute('UPDATE inventory SET amount = amount + ? WHERE item_id = ? AND username = ?', [$quantity, $item->id, $this->name]);
        }
    }

    /**
     * @return iterable<Item>
     */
    public function getInventory(): iterable
    {
        $entries = $this->connection->fetchRows('SELECT inv.*, ip.name FROM inventory inv INNER JOIN items ip ON ip.item_id = inv.item_id WHERE username = ?', [$this->name]);
        foreach ($entries as $entry) {
            yield new Item(
                new ItemPrototype($entry['item_id'], $entry['name'], (int) $entry['worth']),
                $entry['amount']
            );
        }
    }

    private function getProperty(string $property): string|int|float|null
    {
        $result = $this->connection->fetchRow("SELECT {$property} FROM players WHERE name = ?", [$this->name]);
        if ($result === []) {
            throw new \RuntimeException('Player does not exist');
        }

        return $result[$property];
    }

    private function getItemQuantity(int $itemId): int
    {
        $result = $this->connection->fetchRow("SELECT amount FROM inventory WHERE item_id = $itemId AND username = ?", [$this->name]);
        if ($result === []) {
            return 0;
        }

        return (int) $result['amount'];
    }
}
