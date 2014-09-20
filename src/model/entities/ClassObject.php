<?php

namespace model\entities;

require_once('Variable.php');
require_once('Method.php');

class ClassObject {
    const PATTERN = '\[([^\s\]]+?(?=\||\]))((?:\|[^\|\]]*)+)?\]';

    private $name;
    private $attributes = [];
    private $methods = [];

    /**
     * @param string $string
     */
    public function __construct($string) {
        $pattern = '/'.self::PATTERN.'/i';
        preg_match($pattern, $string, $matches);
        $this->name = $matches[1];

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
        var_dump($this->attributes);
        return $this->attributes;
    }

    /**
     * @returns Method[]
     */
    public function getMethods() {
        return $this->methods;
    }
}
