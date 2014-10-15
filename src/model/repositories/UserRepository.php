<?php

namespace model\repositories;

use model\entities\auth\Token;
use model\entities\auth\User;
use model\services\Database;

class UserRepository {

    /**
     * @var Database
     */
    public $database;

    public function __construct(Database $database) {
        $this->database = $database;
        $this->database->assertTable(User::class);
    }

    /**
     * @param User $user
     * @throws \InvalidArgumentException if user isn't valid
     * @throws \DomainException if username is taken
     */
    public function create(User $user) {
        if (!$user->isValid()) {
            throw new \InvalidArgumentException('User is not valid');
        }
        if ($this->getByUsername($user->getUsername())) {
            throw new \DomainException('Username is taken');
        }

        $this->database->insert($user);
    }

    /**
     * @param string $username
     * @return User|null An user object or null if not found
     */
    public function getByUsername($username) {
        return $this->database->select(User::class, '`username` = ?', [$username], 1);
    }

    /**
     * @param Token $token
     * @return User|null An user object or null if not found
     */
    public function getByToken(Token $token) {
        return $this->database->get(User::class, $token->getUserId());
    }
}
