<?php

namespace model\entities;

use model\entities\auth\User;

class Diagram {
    const TOO_SHORT = 1;
    const TOO_LONG = 2;

    private $id;

    /**
     * @var string
     * [column varchar(20)]
     */
    private $name;

    /**
     * @var string
     * [column text]
     */
    private $umls;

    /**
     * @var int
     * [column int(11)]
     */
    private $userId;

    public function __construct($id, User $user) {
        $this->id = $id;
        $this->userId = $user->getId();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name [3, 20]
     * @throws \InvalidArgumentException If at least one of the rules are not met
     */
    public function setName($name) {
        $length = mb_strlen($name);
        if ($length < 3) {
            throw new \InvalidArgumentException(3, self::TOO_SHORT);
        } elseif ($length > 20) {
            throw new \InvalidArgumentException(20, self::TOO_LONG);
        }

        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUmls() {
        return $this->umls;
    }

    /**
     * @param string $umls
     */
    public function setUmls($umls) {
        $this->umls = $umls;
    }

    /**
     * @return bool
     */
    public function isValid() {
        return !empty($this->name);
    }
}
