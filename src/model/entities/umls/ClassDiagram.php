<?php

namespace model\entities\umls;

class ClassDiagram {
    const COMMENT_PATTERN = '%//.*$%m';

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
        $string = preg_replace(self::COMMENT_PATTERN, '', $string);
        preg_match_all('/'.ClassObject::PATTERN.'/i', $string, $classMatches, PREG_SET_ORDER);

        foreach($classMatches as $classMatch) {
            $name = $classMatch[1];
            $class = $this->getClass($name);
            if ($class === null) {
                $this->classes[] = new ClassObject($classMatch[0]);
            } else {
                $this->replaceClass($name, new ClassObject($classMatch[0], $class));
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

    /**
     * @param string $name
     * @return ClassObject The class object if exists, else null
     */
    public function getClass($name) {
        foreach ($this->classes as $class) {
            if ($class->getName() === $name) {
                return $class;
            }
        }
        return null;
    }

    /**
     * Replaces the class named $name with $new
     *
     * @param string $name
     * @param ClassObject $new
     */
    private function replaceClass($name, ClassObject $new) {
        foreach ($this->classes as $index => $class) {
            if ($class->getName() === $name) {
                $this->classes[$index] = $new;
            }
        }
    }
}
