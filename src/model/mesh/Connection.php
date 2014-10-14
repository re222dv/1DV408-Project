<?php

namespace model\mesh;

use model\entities\Association;

class Connection {
    public $data;
    /**
     * @var Node
     */
    public $parent;
    /**
     * @var Node
     */
    public $child;

    public function __construct(Association $association) {
        $this->data = $association;
    }
}
