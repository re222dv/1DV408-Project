<?php

namespace view\point_graph;

const C1 = 2, C2 = 1, C3 = 1, C4 = 0.1;
const M = 100;

trait Graph {
    /**
     * @param Node[] $nodes
     */
    public function calculatePositions($nodes) {
        $count = 2; // Do two times so that all nodes surly have all important nodes
        while ($count--) {
            foreach ($nodes as $node) {
                $node->findTree();
            }
            foreach ($nodes as $node) {
                $node->fix();
            }
        }

        $this->positionVertically($nodes);

        $positionedNodes = $this->positionTopNodes($nodes);
        while ($positionedNodes = $this->positionNodesHorizontallyBelow($positionedNodes));

        $this->positionHorizontally($nodes);

        //var_dump($nodes);
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    private function getTopNodes($nodes) {
        foreach ($nodes as $node) {
            if (!$node->isTopNode()) {
                unset($nodes[$node->getName()]);
            }
        }

        return $nodes;
    }

    /**
     * @param Node[] $nodes
     */
    private function positionHorizontally($nodes) {
        $maxTries = 200;
        do {
            $changed = false;

            foreach ($nodes as $node) {
                foreach ($nodes as $node2) {
                    if ($node === $node2) {
                        continue;
                    }

                    if ($node->collidesWith($node2)) {
                        $node->escape($node2);
                        $changed = true;
                    }
                }
            }
        } while($changed && $maxTries--);

        if ($changed) {
            // TODO: Still not correct
        }
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    private function positionNodesHorizontallyBelow($nodes) {
        /** @var Node[] $positionedNodes */
        $positionedNodes = [];

        foreach ($nodes as $node) {
            foreach ($node->nodesBelow as $below) {
                $positionedNodes[$below->getName()] = $below;
            }
        }

        foreach ($positionedNodes as $node) {
            $node->positionHorizontally();
        }

        return $positionedNodes;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    private function positionTopNodes($nodes) {
        $topNodes = $this->getTopNodes($nodes);
        $names = array_keys($topNodes);

        for ($i = 1; $i < count($names); $i += 1) {
            $topNodes[$names[$i]]->setRightOf($topNodes[$names[$i - 1]]);
        }

        return $topNodes;
    }

    /**
     * @param Node[] $nodes
     */
    private function positionVertically($nodes) {
        $maxTries = 200;
        do {
            $changed = false;

            foreach ($nodes as $node) {
                if (!$node->isVerticalPositionAllowed()) {
                    $node->positionVertically();
                    $changed = true;
                }
            }
        } while($changed && $maxTries--);

        if ($changed) {
            // TODO: Still not correct
        }
    }
}
