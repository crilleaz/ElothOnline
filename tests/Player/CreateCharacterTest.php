<?php
declare(strict_types=1);

namespace Game\Player;

use Game\Auth\AuthService;
use Game\IntegrationTestCase;

class CreateCharacterTest extends IntegrationTestCase
{
    private CreateCharacter $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creator = $this->getService(CreateCharacter::class);
    }

    public function testSuccessfulCreation(): void
    {
        $authService = $this->getService(AuthService::class);
        $authService->register('SomeUserName', 'SomePassword');
        $authService->login('SomeUserName', 'SomePassword');
        $user = $authService->getCurrentUser();

        $race = new Race(
            123,
            'Boblin',
            'We reckon, every rpg has to have boblins',
            new Stats(50, 5, 9),
            new Perks(false, true, true, false, true, false)
        );

        $this->creator->execute('Spiky', $race, $user);

        $this->assertNewCharacterCreated('Spiky', $race);
    }

    private function assertNewCharacterCreated(string $characterName, Race $expectedRace): void
    {
        $character = $this->getService(CharacterRepository::class)->getByName($characterName);

        self::assertEquals($characterName, $character->getName());
        self::assertEquals(1, $character->getLevel());
        self::assertEquals(0, $character->getExp());
        self::assertEquals(100, $character->getStamina());
        self::assertEquals($expectedRace->stats->maxHealth, $character->getCurrentHealth());
        self::assertEquals($expectedRace->stats->maxHealth, $character->getMaxHealth());
        self::assertEquals($expectedRace->stats->strength, $character->getStrength());
        self::assertEquals($expectedRace->stats->defence, $character->getDefence());
        self::assertEquals(10, $character->getGold());
        self::assertFalse($character->isFighting());
        self::assertTrue($character->isInProtectiveZone());
        $lumberjackLvl = $expectedRace->perks->canWoodcut ? 1 : 0;
        $minerLvl = $expectedRace->perks->canMine ? 1 : 0;
        $craftsmanLvl = $expectedRace->perks->canCraft ? 1 : 0;
        $gathererLvl = $expectedRace->perks->canGather ? 1 : 0;
        $farmerLvl = $expectedRace->perks->canHarvest ? 1 : 0;
        $alchemistLvl = $expectedRace->perks->canBrew ? 1 : 0;
        self::assertEquals($lumberjackLvl, $character->getWoodcutting());
        self::assertEquals($alchemistLvl, $character->getAlchemy());
        self::assertEquals($farmerLvl, $character->getHarvesting());
        self::assertEquals($craftsmanLvl, $character->getBlacksmith());
        self::assertEquals($minerLvl, $character->getMining());
        self::assertEquals($gathererLvl, $character->getGathering());
    }
}
