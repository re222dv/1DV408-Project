<?php

namespace model\entities;

class Association {
    private $name;
    private $from;
    private $to;

    /**
     * @param string $string
     */
    public function __construct($string) {
        $pattern = '/'.ClassObject::PATTERN.'-(?:(\w+)-)?'.ClassObject::PATTERN.'/i';

        preg_match($pattern, $string, $matches);

        $this->name = $matches[3];
        $this->from = $matches[1];
        $this->to = $matches[4];
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
    public function getFrom() {
        return $this->from;
    }

    /**
     * @returns string
     */
    public function getTo() {
        return $this->to;
    }
}
