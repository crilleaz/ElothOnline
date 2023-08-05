<?php
declare(strict_types=1);

namespace Game;

class Player
{
    private readonly PlayerLog $logger;

    public static function loadCurrentPlayer(DBConnection $connection): self
    {
        return self::loadPlayer($_SESSION['username'], $connection);
    }

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
        return $this->getHuntingDungeonId() === $dungeon->id;
    }

    public function getHuntingDungeonName(): string
    {
        $dungeonId = $this->getHuntingDungeonId();
        if ($dungeonId === 0) {
            return '';
        }

        $dungeon = $this->connection->fetchRow("SELECT name FROM dungeons WHERE id = {$dungeonId}");

        return $dungeon['name'];
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
        $expGrid = $this->connection->fetchRow("SELECT experience FROM exp_table WHERE level = $nextLvl");
        if ($expGrid === []) {
            throw new \RuntimeException('Level not found');
        }

        return (int) $expGrid['experience'];
    }

    public function addExp(int $amount): void
    {
        $this->connection->execute("UPDATE players SET experience = experience + $amount WHERE name = '{$this->name}'");

        // TODO belongs to some separate exp-grid class
        $expGrid = $this->connection->fetchRow("SELECT level FROM exp_table WHERE experience<={$this->getExp()} ORDER BY level DESC LIMIT 1");
        if ($expGrid === []) {
            throw new \RuntimeException('Level not found');
        }
        $level = (int) $expGrid['level'];

        // Update max level
        $this->connection->execute("UPDATE players SET level = {$level} WHERE name = '{$this->name}'");

        // Update max hp
        $amountToAdd = 15;
        $maxHealth = $level * $amountToAdd;
        $this->connection->execute("UPDATE players SET health_max = {$maxHealth} WHERE name = '{$this->name}'");
    }

    public function getLevel(): int
    {
        return (int) $this->getProperty('level');
    }

    public function getGold(): int
    {
        return $this->getItemQuantity(ItemId::GOLD);
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

    public function getDefense(): int
    {
        return (int) $this->getProperty('defense');
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

    /**
     * @return iterable<Item>
     */
    public function getInventory(): iterable
    {
        $entries = $this->connection->fetchRows("SELECT * FROM inventory WHERE username = ?", [$this->name]);

        foreach ($entries as $entry) {
            yield new Item(ItemId::from((int)$entry['item_id']), (int) $entry['amount'], (int) $entry['worth']);
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

    private function getItemQuantity(ItemId $itemId): int
    {
        $result = $this->connection->fetchRow("SELECT amount FROM inventory WHERE item_id = {$itemId->value} AND username = ?", [$this->name]);
        if ($result === []) {
            return 0;
        }

        return (int) $result['amount'];
    }

    private function getHuntingDungeonId(): int
    {
        $hunt = $this->connection->fetchRow("SELECT dungeon_id FROM hunting WHERE username = ?", [$this->name]);

        return (int) ($hunt['dungeon_id'] ?? 0);
    }
}