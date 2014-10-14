<?php

namespace model\diagrams;

use model\entities\Association;
use model\entities\ClassObject;

class ClassDiagram {
    /**
     * @var ClassObject[]
     */
    private $classes = [];
    /**
     * @var Association[]
     */
    private $associations = [];

    /**
     * @param string $string The string to parse
     */
    public function __construct($string) {
        preg_match_all('/'.ClassObject::PATTERN.'/i', $string, $classMatches, PREG_SET_ORDER);

        foreach($classMatches as $classMatch) {
            $name = $classMatch[1];
            if (isset($this->classes[$name])) {
                $this->classes[$name] = new ClassObject($classMatch[0], $this->classes[$name]);
            } else {
                $this->classes[$name] = new ClassObject($classMatch[0]);
            }
        }

        preg_match_all(Association::getPattern(), $string, $associationMatches, PREG_SET_ORDER);

        foreach($associationMatches as $associationMatch) {
            $this->associations[] = new Association($associationMatch[0]);
        }
    }

    /**
     * @returns ClassObject[] Assoc array name => object
     */
    public function getClasses() {
        return $this->classes;
    }

    /**
     * @returns Association[]
     */
    public function getAssociations() {
        return $this->associations;
    }
}
