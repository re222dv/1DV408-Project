<?php

namespace view\point_graph;

trait Node {
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
        $this->find($this);
    }

    public function fix() {
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
    private function find($node) {
        unset($node->nodesAbove[$node->getName()]);
        unset($node->nodesBelow[$node->getName()]);

        foreach ($node->nodesBelow as $below) {
            if ($this->inArray($node, $below->nodesBelow) ||  // If $node exists below $below
                $this->inArray($below, $node->nodesAbove)) {  // Or $below exists above $node
                continue;
            }

            $node->nodesBelow[$below->getName()] = $below;
            $this->addAll($node->nodesAbove, $below->nodesAbove);
            $this->find($below);
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
            /** @var Node[] $nodesAbove */
            $nodesAbove = $this->diff($nodeToMove->linksIncoming, $nodeToMove->nodesBelow);

            //var_dump(count($nodeToMove->nodesAbove));
            if (count($nodesAbove) === 1 &&
                count(array_values($nodesAbove)[0]->linksOutgoing) > 1) {
                /** @var Node $parent */
                $parent = array_values($nodesAbove)[0];
                $siblings = $this->diff($parent->linksOutgoing, $parent->nodesAbove);
                $width = -20;

                foreach ($siblings as $node) {
                    $width += $node->width + 20;
                }

                $x = $parent->centerX() - $width / 2;
                $x = max(0, $x);

                foreach ($siblings as $node) {
                    $node->x = $x;
                    $x += $node->width + 20;
                }
            }
        }

        if ($this->collidesWith($node)) {
            $length = ($this->width + $node->width) / 4 + 10;
            $this->x -= $length;
            $node->x += $length;

            if ($this->x < 0) {
                $node->x -= $this->x;
                $this->x = 0;
            }
        }
    }

    public function isVerticalPositionAllowed() {
        foreach ($this->nodesAbove as $node) {
            if (!$this->isBelow($node)) {
                return false;
            }
        }

        return true;
    }

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
    }

    public function positionVertically() {
        $this->y = 0;

        foreach ($this->nodesAbove as $node) {
            if ($node->y + $node->height >= $this->y) {
                $this->y = max($this->y, $node->y + $node->height);
            }
        }

        $this->y += 50;
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

    private function inArray($needle, $haystack) {
        foreach($haystack as $straw) {
            if ($straw === $needle) {
                return true;
            }
        }
        return false;
    }

    private function addAll($array, &$to) {
        foreach ($array as $key => $value) {
            $to[$key] = $value;
        }
    }

    /**
     * @param Node[] $minuend
     * @param Node[] $subtrahend
     * @return Node[]
     */
    private function diff(array $minuend, array $subtrahend) {
        foreach ($subtrahend as $removal) {
            unset($minuend[$removal->getName()]);
        }

        return $minuend;
    }

    /**
     * @param Node $other
     */
    public function setRightOf($other) {
        if (!$this->isRightOf($other)) {
            $this->x = $other->x + $other->width + 20;
        }
    }
}
