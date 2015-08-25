<?php

namespace zibo\library\image;

/**
 * Data container for a point
 */
class Point {

    /**
     * Value on the X-axis
     * @var integer
     */
    private $x;

    /**
     * Value on the Y-axis
     * @var integer
     */
    private $y;

    /**
     * Constructs a new point
     * @param integer $x
     * @param integer $y
     * @return null
     */
    public function __construct($x, $y) {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Gets a string representation of this point
     * @return string;
     */
    public function __toString() {
        return '(' . $this->x . ', ' . $this->y . ')';
    }

    /**
     * Gets the value on the X-axis
     * @return integer
     */
    public function getX() {
        return $this->x;
    }

    /**
     * Gets the value on the Y-axis
     * @return integer
     */
    public function getY() {
        return $this->y;
    }

    /**
     * Gets whether the provided point equals this point
     * @param Point $point Point to compare with
     * @return boolean True if the point is the same, false otherwise
     */
    public function equals(Point $point) {
        return $this->x == $point->x && $this->y == $point->y;
    }

}