<?php

namespace model\mesh;

use model\entities\ClassObject;

class Node {
    public $data;
    /**
     * Connection[]
     */
    private $parents = [];
    /**
     * Connection[]
     */
    private $children = [];

    public function __construct(ClassObject $class) {
        $this->data = $class;
    }

    public function addParent(Connection $connection) {
        $this->parents[] = $connection;
    }

    public function addChild(Connection $connection) {
        $this->children[] = $connection;
    }

    public function getParents() {
        return $this->parents;
    }

    public function getChildren() {
        return $this->children;
    }
}
