<?php

namespace controller;

use model\repositories\UserRepository;
use model\services\Auth;
use view\auth\LoginView;
use view\auth\RegisterView;
use view\auth\UserView;
use view\services\Router;

class AuthController {
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var LoginView
     */
    private $loginView;
    /**
     * @var RegisterView
     */
    private $registerView;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserView
     */
    private $userView;

    public function __construct(Auth $auth, Router $router, UserRepository $userRepository,
                                LoginView $loginView, RegisterView $registerView,
                                UserView $userView) {
        $this->auth = $auth;
        $this->router = $router;
        $this->userRepository = $userRepository;
        $this->loginView = $loginView;
        $this->registerView = $registerView;
        $this->userView = $userView;
    }

    /**
     * @return RegisterView
     */
    public function register() {
        if ($this->registerView->haveRegistered()) {
            $user = $this->registerView->getUser();

            if ($user->isValid()) {
                try {
                    $this->userRepository->create($user);

                    $this->auth->login($user);
                    $this->router->redirectTo(Router::INDEX);
                } catch (\DomainException $e) {
                    $this->registerView->addUsernameExistsError();
                }
            }
        }

        return $this->registerView;
    }

    /**
     * @return LoginView|UserView
     */
    public function render() {
        if ($this->auth->isLoggedIn() && $this->userView->haveLoggedOut()) {
            $this->auth->logOut();
            $this->router->redirectTo(Router::INDEX);
        } elseif (!$this->auth->isLoggedIn() && $this->loginView->haveLoggedIn()) {
            $username = $this->loginView->getUsername();
            $password = $this->loginView->getPassword();

            if ($this->auth->logInByCredentials($username, $password)) {
                $this->router->redirectTo(Router::INDEX);
            } else {
                $this->loginView->loginFailed();
            }
        }

        if ($this->auth->isLoggedIn()) {
            return $this->userView;
        } else {
            return $this->loginView;
        }
    }
}
