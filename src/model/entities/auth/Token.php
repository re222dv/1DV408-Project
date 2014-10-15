<?php

namespace model\entities\auth;

class Token {
    /**
     * @var string
     * [column varchar(512)]
     */
    private $secret;

    /**
     * @var int
     * [column int(11)]
     */
    private $expirationDate;

    /**
     * @var int
     * [column int(11)]
     */
    private $userId;

    public function __construct(User $user) {
        $this->secret = openssl_random_pseudo_bytes(512);
        $this->expirationDate = strtotime('+7 days');
        $this->userId = $user->getId();
    }

    /**
     * @return string
     */
    public function getSecret() {
        return $this->secret;
    }

    /**
     * @return int
     */
    public function getExpirationDate() {
        return $this->expirationDate;
    }

    /**
     * @return int
     */
    public function getUserId() {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function isValid() {
        return $this->expirationDate > time();
    }
}
