<?php

namespace view\services\spring;

trait Node {
    private $friends = [];
    public $x = 0;
    public $y = 0;
    public $width = 0;
    public $height = 0;

    /**
     * @param Node $friend
     */
    public function addFriend($friend) {
        $this->friends[$friend->getName()] = $friend;
    }

    /**
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

    /**
     * @param Node[] $nodes All vertexes in the graph with the name as key
     */
    public function calculatePositionByForce(array $nodes) {
        $enemies = array_diff_key($nodes, $this->friends);

        $newPoint = $this->copy();

        foreach ($this->friends as $friend) {
            $distance = $this->calculateDistance($friend);
            $force = calculatePositiveForce($distance);
            $newPoint->move($friend, $force);
        }

        foreach ($enemies as $enemy) {
            $distance = $this->calculateDistance($enemy);
            $force = calculateNegativeForce($distance);
            $newPoint->move($enemy, $force);
        }

        $this->x = $newPoint->x;
        //$this->y = $newPoint->y;
    }

    public function copy() {
        return new NodeCopy($this);
    }

    public function centerX() {
        return $this->x + $this->width / 2;
    }

    public function centerY() {
        return $this->y + $this->height / 2;
    }

    abstract public function getName();

    /**
     * @param Node $towards
     * @param number $distance
     */
    public function move($towards, $distance) {
        $angle = atan2($towards->centerY() - $this->centerY(),
            $towards->centerX() - $this->centerX());
        $this->x += cos($angle) * $distance;
        $this->y += sin($angle) * $distance;
    }
}

class NodeCopy {
    use Node;

    private $name;

    /**
     * @param Node $original
     */
    public function __construct($original) {
        $this->x = $original->x;
        $this->y = $original->y;
        $this->width = $original->width;
        $this->height = $original->height;
        $this->name = $original->getName();
    }

    public function getName() {
        return $this->name;
    }
}

/**
 * @param number $distance
 * @return number
 */
function calculatePositiveForce($distance) {
    return C1 * log($distance/C2);
}

/**
 * @param number $distance
 * @return number
 */
function calculateNegativeForce($distance) {
    return -(C3 * sqrt($distance));
}
