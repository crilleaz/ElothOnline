<?php
declare(strict_types=1);

namespace Game\Player;

use Game\IntegrationTestCase;

class CharacterRepositoryTest extends IntegrationTestCase
{
    private CharacterRepository $characterRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->characterRepository = $this->getService(CharacterRepository::class);
    }

    public function testListTopPlayers(): void
    {
        $lvls = [
            'MisterTester1' => LvlCalculator::minExpRequired(10),
            'MisterTester2' => LvlCalculator::minExpRequired(5),
            'MisterTester3' => LvlCalculator::minExpRequired(3),
            'MisterTester4' => LvlCalculator::minExpRequired(4),
            'MisterTester5' => LvlCalculator::minExpRequired(7),
            'MisterTester6' => LvlCalculator::minExpRequired(2),
            'MisterTester7' => LvlCalculator::minExpRequired(12),
            'MisterTester8' => LvlCalculator::minExpRequired(9),
            'MisterTester9' => LvlCalculator::minExpRequired(1),
            'MisterTester10' => LvlCalculator::minExpRequired(6),
        ];

        foreach($lvls as $newPlayerName => $exp) {
            $character = $this->createCharacter($newPlayerName);
            $character->addExp($exp);
        }

        $topCharNames = [];
        foreach ($this->characterRepository->listTopCharacters(7) as $topChar) {
            $topCharNames[] = $topChar->getName();
        }

        self::assertSame(
            [
                'MisterTester7',
                'MisterTester1',
                'MisterTester8',
                'MisterTester5',
                'MisterTester10',
                'MisterTester2',
                'MisterTester4',
            ],
            $topCharNames
        );
    }
}
