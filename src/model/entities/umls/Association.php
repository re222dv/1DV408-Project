<?php

namespace model\entities\umls;

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

        $this->name = $matches[ClassObject::PATTERN_GROUP_COUNT + 1];
        $this->from = $matches[ClassObject::PATTERN_NAME_GROUP];
        $this->to = $matches[ClassObject::PATTERN_GROUP_COUNT + 1 + ClassObject::PATTERN_NAME_GROUP];
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
