<?php

namespace model\diagrams;

use model\entities\Association;
use model\entities\ClassObject;

class ClassDiagram {
    private $classes = [];
    private $associations = [];

    /**
     * @param string $string
     */
    public function __construct($string) {
        preg_match_all('/'.ClassObject::PATTERN.'/i', $string, $classMatches, PREG_SET_ORDER);

        foreach($classMatches as $classMatch) {
            $name = $classMatch[1];
            if (!in_array($name, $this->classes)) {
                $this->classes[$name] = new ClassObject($classMatch[0]);
            }
        }

        preg_match_all(Association::getPattern(), $string, $associationMatches, PREG_SET_ORDER);

        foreach($associationMatches as $associationMatch) {
            $this->associations[] = new Association($associationMatch[0]);
        }
    }

    /**
     * @returns ClassObject[]
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
