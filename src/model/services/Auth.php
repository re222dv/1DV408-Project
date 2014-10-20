<?php

namespace model\services;

use model\entities\auth\Token;
use model\entities\auth\User;
use model\repositories\UserRepository;

class Auth {
    const SV_USERNAME = 'Auth::username';

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var User
     */
    private $user;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;

        session_start();
    }

    /**
     * @param User $user
     */
    public function login(User $user) {
        $this->user = $user;
        $this->setSession($user->getUsername());
    }

    /**
     * Try to login by username and password
     *
     * @param string $username
     * @param string $password
     * @return User|null the logged in User on success or null on fail
     */
    public function logInByCredentials($username, $password) {
        $user = $this->userRepository->getByUsername($username);

        if ($user and $user->verifyPassword($password)) {
            $this->login($user);
            return $user;
        }

        return null;
    }

    /**
     * Try to login by Token
     *
     * @param Token $token
     * @return User|null the logged in User on success or null on fail
     */
    public function logInByToken(Token $token) {
        $user = $this->userRepository->getByToken($token);

        if ($user) {
            $this->login($user);
            return $user;
        }

        return null;
    }

    public function logOut() {
        $this->user = null;
        $this->setSession(null);
    }

    public function isLoggedIn() {
        return $this->getSession() != null;
    }

    /**
     * @return User|null null if not logged in
     */
    public function getUser() {
        if ($this->isLoggedIn() && !$this->user) {
            $this->user = $this->userRepository->getByUsername($this->getSession());
        }

        return $this->user;
    }

    private function getSession() {
        if (isset($_SESSION[self::SV_USERNAME])) {
            return $_SESSION[self::SV_USERNAME];
        }
        return null;
    }

    private function setSession($username) {
        $_SESSION[self::SV_USERNAME] = $username;
    }
}
