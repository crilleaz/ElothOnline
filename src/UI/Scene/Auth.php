<?php

declare(strict_types=1);

namespace Game\UI\Scene;

use Game\Auth\AuthService;
use Game\UI\Scene\Input\InputInterface;
use Twig\Environment;

readonly class Auth implements SceneInterface
{
    public function __construct(private AuthService $authService, private Environment $renderer)
    {
    }

    public function run(InputInterface $input): string
    {
        $currentTab = $input->getString('tab');

        if ($currentTab === 'register') {
            return $this->registerTab($input);
        }

        if ($currentTab === 'logout') {
            $this->authService->logout();
            header('Location: /');

            return '';
        }

        return $this->loginTab($input);
    }

    private function loginTab(InputInterface $input): string
    {
        $error    = '';
        $username = '';
        if ($input->getString('login') !== '') {
            $username = $input->getString('username');
            $password = $input->getString('password');

            if ($username === '' || $password === '') {
                $error = 'Username or password can not be empty';
            } else {
                $result = $this->authService->login($username, $password);
                if ($result === null) {
                    // todo Kind of meh. Think of smother toggle between scenes
                    header('Location: /');

                    return '';
                }

                $error = $result->message;
            }
        }

        return $this->renderer->render('login.html.twig', [
            'username' => $username,
            'error'    => $error,
        ]);
    }

    private function registerTab(InputInterface $input): string
    {
        $error    = '';
        $username = '';
        if ($input->getString('register') !== '') {
            $username        = $input->getString('username');
            $password        = $input->getString('password');
            $passwordConfirm = $input->getString('password_confirm');

            if ($username === '' || $password === '' || $passwordConfirm === '') {
                $error = 'All fields are required';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Passwords do not match';
            } else {
                $result = $this->authService->register($username, $password);
                if ($result === null) {
                    // Auto login on registration
                    $this->authService->login($username, $password);

                    // todo Kind of meh. Think of smother toggle between scenes
                    header('Location: /');

                    return '';
                }

                $error = $result->message;
            }
        }

        return $this->renderer->render('register.html.twig', [
            'username' => $username,
            'error'    => $error,
        ]);
    }
}
