<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Auth\AuthService;
use Game\Client;
use Game\Player\CreateCharacter;
use Game\Player\RaceRepository;
use Game\UI\Scene\Input\HttpInput;
use Game\UI\Scene\Input\InputInterface;
use Game\User;
use Twig\Environment;

readonly class CharacterCreation implements SceneInterface
{
    public function __construct(
        private Client $client,
        private Environment $renderer,
        private RaceRepository $raceRepository,
        private CreateCharacter $createCharacter,
        private AuthService $authService
    ) {
    }

    public function run(InputInterface $input): string
    {
        $character = $this->client->getCurrentPlayer();
        if ($character !== null) {
            return $this->switchToMainMenu();
        }

        $error        = '';
        $selectedRace = $input->getInt('race');
        if ($selectedRace > 0) {
            $user = $this->getCurrentUser();
            $race = $this->raceRepository->getById($selectedRace);
            $this->createCharacter->execute($user->name, $race, $user);

            return $this->switchToMainMenu();
        }

        $races = iterable_to_array($this->raceRepository->listAll());

        return $this->renderer->render('character-creation.html.twig', [
            'races' => $races,
            'error' => $error,
        ]);
    }

    private function switchToMainMenu(): string
    {
        /** @var SceneInterface $scene */
        $scene = \DI::getService(MainMenu::class);

        return $scene->run(new HttpInput());
    }

    private function getCurrentUser(): User
    {
        $user = $this->authService->getCurrentUser();

        if ($user === null) {
            throw new \RuntimeException('Unauthenticated access was not expected');
        }

        return $user;
    }
}
