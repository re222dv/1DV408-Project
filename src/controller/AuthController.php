<?php

namespace controller;

use model\entities\auth\Token;
use model\repositories\TokenRepository;
use model\repositories\UserRepository;
use model\services\Auth;
use view\auth\LoginView;
use view\auth\RegisterView;
use view\auth\UserView;
use view\services\CredentialsHandler;
use view\services\Router;

class AuthController {
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var CredentialsHandler
     */
    private $credentialsHandler;
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
     * @var TokenRepository
     */
    private $tokenRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserView
     */
    private $userView;

    public function __construct(Auth $auth, CredentialsHandler $credentialsHandler, Router $router,
                                TokenRepository $tokenRepository, UserRepository $userRepository,
                                LoginView $loginView, RegisterView $registerView,
                                UserView $userView) {
        $this->auth = $auth;
        $this->credentialsHandler = $credentialsHandler;
        $this->router = $router;
        $this->tokenRepository = $tokenRepository;
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
            $this->credentialsHandler->deleteSecret();
            $this->router->redirectTo(Router::INDEX);
        } elseif (!$this->auth->isLoggedIn()) {
            if ($this->loginView->haveLoggedIn()) {
                $username = $this->loginView->getUsername();
                $password = $this->loginView->getPassword();

                if ($this->auth->logInByCredentials($username, $password)) {
                    $token = new Token($this->auth->getUser());
                    $this->tokenRepository->insert($token);
                    $this->credentialsHandler->saveSecret($token);
                    $this->router->redirectTo(Router::INDEX);
                } else {
                    $this->loginView->loginFailed();
                }
            } elseif ($this->credentialsHandler->hasSecret()) {
                $secret = $this->credentialsHandler->getSecret();
                try {
                    $token = $this->tokenRepository->getBySecret($secret);
                    $this->auth->logInByToken($token);
                    $this->router->redirectTo(Router::INDEX);
                } catch (\Exception $e) {}
            }
        }

        if ($this->auth->isLoggedIn()) {
            return $this->userView;
        } else {
            return $this->loginView;
        }
    }
}
