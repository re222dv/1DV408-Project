<?php

namespace model\mesh;

use model\diagrams\ClassDiagram;

class Network {
    /**
     * @var Node[]
     */
    private $topNodes = [];
    /**
     * @var ClassDiagram
     */
    private $classDiagram;

    public function __construct(ClassDiagram $classDiagram) {
        $this->classDiagram = $classDiagram;

        /** @var Node[] $nodes */
        $nodes = [];

        foreach ($classDiagram->getClasses() as $name => $class) {
            $nodes[$name] = new Node($class);
        }

        foreach ($classDiagram->getAssociations() as $association) {
            $from = $nodes[$association->getFrom()];
            $to = $nodes[$association->getTo()];

            $connection = new Connection($association);
            $connection->parent = $from;
            $connection->child = $to;

            $from->addChild($connection);
            $to->addParent($connection);
        }

        $this->topNodes = $this->findTopNodes($nodes);
    }

    public function getTopNodes() {
        return $this->topNodes;
    }

    /**
     * @param Node[] $nodes
     * @return Node[]
     */
    private function findTopNodes(array $nodes) {
        $topNodes = [];

        foreach ($nodes as $node) {
            // Nodes with no parents are safely assured as top nodes
            if (count($node->getParents()) === 0) {
                $topNodes[] = $node;
            }
        }
        $nodes = $this->subtractNodes($nodes, $topNodes);
        $nodes = $this->subtractDeepChildren($nodes, $topNodes);

        // Count all remaining nodes as top nodes
        $topNodes = array_merge($topNodes, $nodes);

        return $topNodes;
    }

    private function subtractNodes(array $minuend, array $subtrahend) {
        return array_udiff($minuend, $subtrahend, function($a, $b) {return $a === $b;});
    }

    private function subtractDeepChildren(array $nodes, array $topNodes) {
        $nodesToSubtract = [];

        foreach ($topNodes as $node) {
            $nodesToSubtract = array_merge($nodesToSubtract, $this->getDeepChildren($node));
        }

        return $this->subtractNodes($nodes, $nodesToSubtract);
    }

    /**
     * @param Node $node
     * @return Node[]
     */
    private function getDeepChildren(Node $node) {
        $children = [];

        foreach ($node->getChildren() as $child) {
            $children[] = $child->child;
            $children = array_merge($children, $this->getDeepChildren($child->child));
        }

        return $children;
    }
}
