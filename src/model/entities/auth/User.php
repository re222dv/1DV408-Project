<?php

namespace model\entities\auth;

class User {
    const TOO_SHORT = 1;
    const TOO_LONG = 2;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * [column varchar(20)]
     */
    private $username;

    /**
     * @var string
     * [column varchar(256)]
     */
    private $hash;

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username [3, 20]
     * @throws \InvalidArgumentException If at least one of the rules are not met
     */
    public function setUsername($username) {
        $length = mb_strlen($username);
        if ($length < 3) {
            throw new \InvalidArgumentException(3, self::TOO_SHORT);
        } elseif ($length > 20) {
            throw new \InvalidArgumentException(20, self::TOO_LONG);
        }

        $this->username = $username;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->hash = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isValid() {
        return !empty($this->username) && !empty($this->hash);
    }

    /**
     * @param string $password
     * @return bool
     */
    public function verifyPassword($password) {
        return password_verify($password, $this->hash);
    }
}
