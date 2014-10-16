<?php

namespace view\services\spring;

const C1 = 2, C2 = 1, C3 = 1, C4 = 0.1;
const M = 100;

trait Graph {
    public function applyForce() {
        $nodes = $this->getNodes();

        for ($i = 0; $i < M; $i += 1) {
            foreach ($nodes as $node) {
                $node->calculatePositionByForce($nodes);
            }
        }
    }

    /**
     * @return Node[] All nodes in the graph with name as keys
     */
    abstract public function getNodes();
}
