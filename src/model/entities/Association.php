<?php

namespace model\entities;

class Association {
    private $name;
    private $from;
    private $to;

    public static function getPattern() {
        return '/'.ClassObject::PATTERN.'-(?:(\w+)-)?'.ClassObject::PATTERN.'/i';
    }

    /**
     * @param string $string The string to parse
     */
    public function __construct($string) {
        $pattern = self::getPattern();

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
     * @returns string Name of the class this association goes from
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * @returns string Name of the class this association goes to
     */
    public function getTo() {
        return $this->to;
    }
}
