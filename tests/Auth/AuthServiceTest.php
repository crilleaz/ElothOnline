<?php
declare(strict_types=1);

namespace Game\Auth;

use Game\IntegrationTestCase;

class AuthServiceTest extends IntegrationTestCase
{
    private const PLAYER_NAME = 'MisterTester';
    private const PLAYER_PASSWORD = '123123123';

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authService = $this->getService(AuthService::class);
    }

    public function testRegister(): void
    {
        $result = $this->authService->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->assertNoErrorOccurred($result);
    }

    public function testRegisterWithExistingNickname(): void
    {
        $firstAttempt = $this->authService->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        self::assertNoErrorOccurred($firstAttempt);
        $secondAttempt = $this->authService->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        self::assertErrorOccurred($secondAttempt, 'Username is already taken');
    }

    public function testLoginWhenAccountDoesNotExist(): void
    {
        $result = $this->authService->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);

        self::assertErrorOccurred($result, 'Invalid username or password');
    }

    public function testLoginWithIncorrectPassword(): void
    {
        $this->authService->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->authService->login(self::PLAYER_NAME, 'wrongpassword');

        self::assertErrorOccurred($result, 'Invalid username or password');
    }

    public function testLoginSuccess(): void
    {
        $this->authService->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $result = $this->authService->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->assertNoErrorOccurred($result);
    }

    public function testLoginNotPossibleForBanned(): void
    {
        $this->authService->register(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        $this->authService->banPlayer(self::PLAYER_NAME);

        $result = $this->authService->login(self::PLAYER_NAME, self::PLAYER_PASSWORD);
        self::assertErrorOccurred($result, 'User is banned');
    }
}
