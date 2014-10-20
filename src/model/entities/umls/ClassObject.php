<?php

namespace model\entities\umls;

require_once('Variable.php');
require_once('Method.php');

class ClassObject {
    const PATTERN = '\[([^\s\]]+?(?=\||\]))((?:\|[^\|\]]*)+)?\]';
    const PATTERN_NAME_GROUP = 1;
    const PATTERN_GROUP_COUNT = 2;

    private $name;
    private $attributes = [];
    private $methods = [];

    /**
     * @param string $string The string to parse
     * @param ClassObject $extend optional, set if this ClassObject should extend another one
     */
    public function __construct($string, ClassObject $extend = null) {
        if ($extend !== null) {
            $this->attributes = $extend->getAttributes();
            $this->methods = $extend->getMethods();
        }

        $pattern = '/'.self::PATTERN.'/i';
        preg_match($pattern, $string, $matches);
        $this->name = $matches[self::PATTERN_NAME_GROUP];

        if (count($matches) > 2) {
            $blocks = preg_split('/\|/', $matches[2]);

            // Attributes block
            if (count($blocks) > 1) {
                foreach (mb_split(';', $blocks[1]) as $attribute) {
                    if (!empty($attribute)) {
                        $this->attributes[] = new Variable($attribute);
                    }
                }
            }

            // Methods block
            if (count($blocks) > 2) {
                foreach (mb_split(';', $blocks[2]) as $method) {
                    $this->methods[] = new Method($method);
                }
            }
        }
    }

    /**
     * @returns string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @returns Variable[]
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @returns Method[]
     */
    public function getMethods() {
        return $this->methods;
    }
}
