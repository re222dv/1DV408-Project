<?php

namespace model\entities;

class Variable {
    const PATTERN = '/(\S+?(?=:|\s|$))(?:\s?:\s?([a-z]+))?/i';

    private $name;
    private $type;

    /**
     * @param string $string
     */
    public function __construct($string) {
        preg_match(self::PATTERN, $string, $matches);
        $this->name = $matches[1];
        if (count($matches) > 2) {
            $this->type = $matches[2];
        }
    }

    /**
     * @returns string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @returns string
     */
    public function getType() {
        return $this->type;
    }
}
