<?php

namespace model\entities\umls;

class Method {
    const PATTERN = '/(\S+?(?=\())\(((?:.*?(?=,|\)),?)+)\)(?:\s?:\s?([a-z]+))?/i';

    private $name;
    private $arguments = [];
    private $returnType;

    /**
     * @param string $string The string to parse
     */
    public function __construct($string) {
        preg_match(self::PATTERN, $string, $matches);
        $this->name = $matches[1];

        foreach (mb_split(',', $matches[2]) as $argument) {
            if (!empty($argument)) {
                $this->arguments[] = new Variable($argument);
            }
        }

        if(count($matches) > 3) {
            $this->returnType = $matches[3];
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
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * @returns string
     */
    public function getReturnType() {
        return $this->returnType;
    }
}
