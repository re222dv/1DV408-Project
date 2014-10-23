<?php

namespace view\graph;

trait Node {
    protected static $horizontalMargin = 20;
    protected static $verticalMargin = 80;

    /**
     * @var Node[]
     */
    public $linksIncoming = [];
    /**
     * @var Node[]
     */
    public $linksOutgoing = [];
    /**
     * @var Node[]
     */
    private $nodesAbove = [];
    /**
     * @var Node[]
     */
    public $nodesBelow = [];
    public $x = 0;
    public $y = 0;
    public $width = 0;
    public $height = 0;

    /**
     * Calculates the distance to other Node $to, taking into account width and height
     *
     * @param Node $to
     * @return float
     */
    public function calculateDistance($to) {
        $deltaXLeft = pow($to->x + $to->width - $this->x, 2);
        $deltaXRight = pow($to->x - $this->x + $this->width, 2);
        $deltaYAbove = pow($to->y + $to->height - $this->y, 2);
        $deltaYBelow = pow($to->y - $this->y + $this->height, 2);

        $deltaX = min($deltaXLeft, $deltaXRight);
        $deltaY = min($deltaYAbove, $deltaYBelow);

        return sqrt($deltaX + $deltaY);
    }

    public function centerX() {
        return $this->x + $this->width / 2;
    }

    public function centerY() {
        return $this->y + $this->height / 2;
    }

    public function isFreeNode() {
        return count($this->nodesAbove) === 0 && count($this->nodesBelow) === 0;
    }

    public function isTopNode() {
        return count($this->nodesAbove) === 0 && count($this->nodesBelow) !== 0;
    }

    public function findTree() {
        $this->addAll($this->linksIncoming, $this->nodesAbove);
        $this->addAll($this->linksOutgoing, $this->nodesBelow);
        $this->findNodesBelow($this);
    }

    public function fixTree() {
        foreach ($this->nodesAbove as $above) {
            if ($this->inArray($above, $this->nodesBelow)) {
                // We have the same node above and below

                if (count($this->nodesAbove) > count($this->nodesBelow)) {
                    // We have more nodes above than below so it's probably better to move down
                    $above->nodesBelow[$this->getName()] = $this;
                    unset($above->nodesAbove[$this->getName()]);
                    unset($this->nodesBelow[$above->getName()]);
                    $this->addAll($above->nodesAbove, $this->nodesAbove);
                } else {
                    // Move up
                    $above->nodesAbove[$this->getName()] = $this;
                    unset($above->nodesBelow[$this->getName()]);
                    unset($this->nodesAbove[$above->getName()]);
                    $this->addAll($above->nodesBelow, $this->nodesBelow);
                }
            }
        }

    }

    abstract public function getName();

    /**
     * @param Node $node
     */
    private function findNodesBelow($node) {
        unset($node->nodesAbove[$node->getName()]);
        unset($node->nodesBelow[$node->getName()]);

        foreach ($node->nodesBelow as $below) {
            if ($this->inArray($node, $below->nodesBelow) ||  // If $node exists below $below
                $this->inArray($below, $node->nodesAbove)) {  // Or $below exists above $node
                continue;
            }

            $node->nodesBelow[$below->getName()] = $below;
            $this->addAll($node->nodesAbove, $below->nodesAbove);
            $this->findNodesBelow($below);
            $this->addAll($below->nodesBelow, $node->nodesBelow);
        }
    }

    /**
     * @param Node $node
     * @return bool True if $this does collide with $node
     */
    public function collidesWith($node) {
        return !(
            $this->isBelow($node) ||
            $this->isAbove($node)  ||
            $this->isLeftOf($node) ||
            $this->isRightOf($node)
        );
    }

    private function associationLength() {
        $length = 0;

        foreach (array_merge($this->linksIncoming, $this->linksOutgoing) as $associated) {
            $length += $this->calculateDistance($associated);
        }

        return $length;
    }

    /**
     * @param Node $node
     */
    public function escape($node) {
        $nodeToMove = null;

        if ($this->associationLength() > $node->associationLength()) {
            $nodeToMove = $this;
        } else {
            $nodeToMove = $node;
        }

        $nodeToMove->positionHorizontally();

        if ($this->collidesWith($node)) {
            $nodeToMove->positionSiblingsHorizontally();
        }

        if ($this->collidesWith($node)) {
            $length = ($this->width + $node->width) / 4 + self::$horizontalMargin / 2;

            $left = $this->x < $node->x ? $this : $node;
            $right = $this->x > $node->x ? $this : $node;

            $left->x -= $length;
            $right->x += $length;

            if ($left->x < 0) {
                $right->x -= $left->x;
                $left->x = 0;
            }
        }

        if ($this->collidesWith($node)) {
            // Still colliding, try to move the other node
            $nodeToMove = $this->diff([$this, $node], [$nodeToMove])[0];
            $nodeToMove->positionHorizontally();
        }
    }

    /**
     * @return bool True if this node is below all $nodesAbove
     */
    public function isVerticalPositionAllowed() {
        foreach ($this->nodesAbove as $node) {
            if (!$this->isBelow($node)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Tries to find a good horizontal position using neighbour nodes
     */
    public function positionHorizontally() {
        /** @var Node[] $nodesAbove */
        $nodesAbove = $this->diff($this->linksIncoming, $this->nodesBelow);

        if (count($nodesAbove) > 1) {
            $max = 0;
            $min = max(0, array_values($nodesAbove)[0]->x);

            foreach ($nodesAbove as $node) {
                $max = max($max, $node->x + $node->width);
                $min = min($min, $node->x);
            }

            $this->x = ($max - $min) / 2 - $this->width / 2;
        } elseif (count($nodesAbove) === 1) {
            $above = array_values($nodesAbove)[0];
            $this->x = $above->x;
        }

        foreach($nodesAbove as $node) {
            $commonAncestors = $this->common($this->nodesAbove, $node->nodesAbove);

            foreach ($commonAncestors as $ancestor) {

                if ($this->x + $this->width > $ancestor->centerX() &&
                    $this->x < $ancestor->centerX()) {
                    if ($ancestor->x - $this->x > 0) {
                        $this->x = $ancestor->x - $this->width / 2;
                    } else {
                        $this->x = $ancestor->x + $ancestor->width - $this->width / 2;
                    }
                }
            }
        }
    }

    /**
     * Tries to find a good horizontal position for this and sibling nodes
     * (nodes sharing the same $linksIncoming that should be above them)
     */
    public function positionSiblingsHorizontally() {
        /** @var Node[] $nodesAbove */
        $nodesAbove = $this->diff($this->linksIncoming, $this->nodesBelow);

        if (count($nodesAbove) === 1 &&
            count(array_values($nodesAbove)[0]->linksOutgoing) > 1) {
            /** @var Node $parent */
            $parent = array_values($nodesAbove)[0];
            $siblings = $this->diff($parent->linksOutgoing, $parent->nodesAbove);
            $width = -self::$horizontalMargin;

            foreach ($siblings as $sibling) {
                $width += $sibling->width + self::$horizontalMargin;
            }

            $x = $parent->centerX() - $width / 2;
            $x = max(0, $x);

            foreach ($siblings as $sibling) {
                $sibling->x = $x;
                $x += $sibling->width + self::$horizontalMargin;
            }
        }
    }

    /**
     * Positions this node below the lowest node that is required to be above it
     */
    public function positionVertically() {
        $this->y = 0;

        foreach ($this->nodesAbove as $node) {
            if ($node->y + $node->height >= $this->y) {
                $this->y = max($this->y, $node->y + $node->height);
            }
        }

        $this->y += self::$verticalMargin;
    }

    /**
     * @param Node $other
     */
    public function setRightOf($other) {
        if (!$this->isRightOf($other)) {
            $this->x = $other->x + $other->width + self::$horizontalMargin;
        }
    }

    /**
     * @param Node $other
     * @return bool True if $this is above $other
     */
    private function isAbove($other) {
        return $this->y + $this->height < $other->y;
    }

    /**
     * @param Node $other
     * @return bool True if $this is below $other
     */
    private function isBelow($other) {
        return $this->y > $other->y + $other->height;
    }

    /**
     * @param Node $other
     * @return bool True if $this is left of $other
     */
    private function isLeftOf($other) {
        return $this->x + $this->width < $other->x;
    }

    /**
     * @param Node $other
     * @return bool True if $this is right of $other
     */
    private function isRightOf($other) {
        return $this->x > $other->x + $other->width;
    }

    // Array functions
    /**
     * @param Node $needle
     * @param Node[] $haystack
     * @return bool True if $needle is in $haystack
     */
    private function inArray($needle, array $haystack) {
        foreach($haystack as $straw) {
            if ($straw->getName() === $needle->getName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Node[] $array
     * @param Node[] $to
     */
    private function addAll(array $array, array &$to) {
        foreach ($array as $key => $value) {
            $to[$key] = $value;
        }
    }

    /**
     * @param Node[] $minuend
     * @param Node[] $subtrahend
     * @return Node[] Nodes that exist in $minuend but not in $subtrahend
     */
    private function diff(array $minuend, array $subtrahend) {
        foreach ($subtrahend as $removal) {
            unset($minuend[$removal->getName()]);
        }

        return $minuend;
    }

    /**
     * @param Node[] $left
     * @param Node[] $right
     * @return Node[] Nodes that exists in both $left and $right
     */
    private function common(array $left, array $right) {
        $common = [];

        foreach ($left as $node) {
            if (isset($right[$node->getName()])) {
                $common[] = $node;
            }
        }

        return $common;
    }
}
