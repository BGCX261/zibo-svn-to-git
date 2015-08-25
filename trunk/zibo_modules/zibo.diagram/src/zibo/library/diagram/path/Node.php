<?php

namespace zibo\library\diagram\path;

use zibo\library\image\Point;

/**
 * A node is a potential path point in the grid
 */
class Node {

    /**
     * Point of the grid cell
     * @var zibo\library\image\Point
     */
    private $point;

    /**
     * The id of the parent node
     * @var string
     */
    private $parent;

    /**
     * The total cost from begin to end through this node
     * @var integer
     */
    private $cost;

    /**
     * The cost to move from the begin node to this node
     * @var integer
     */
    private $movementCost;

    /**
     * The estimated cost from this node to the end node
     * @var integer
     */
    private $estimatedMovementCost;

    /**
     * Constructs a new node
     * @param zibo\library\image\Point $point Point of the grid cell
     * @return null
     */
    public function __construct(Point $point) {
        $this->point = $point;
        $this->parent = null;

        $this->cost = 0;
        $this->movementCost = 0;
        $this->estimatedMovementCost = 0;
    }

    /**
     * Gets a string representation of this node
     * @return string
     */
    public function __toString() {
        return '[' . $this->point->__toString() . ' F: ' . $this->cost . '; G: ' . $this->movementCost . '; H: ' . $this->estimatedMovementCost . ']';
    }

    /**
     * Gets the id of this node
     * @return string
     */
    public function getId() {
        return $this->point->__toString();
    }

    /**
     * Gets the point of the grid cell
     * @return zibo\library\image\Point
     */
    public function getPoint() {
        return $this->point;
    }

    /**
     * Sets the parent node
     * @param string $parent Id of the parent node
     * @return null
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Gets the parent node
     * @return string Id of the parent node
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Sets the cost to move from the begin node to this node
     * @param integer $cost
     * @return null
     */
    public function setMovementCost($cost) {
        $this->movementCost = $cost;
        $this->calculateCost();
    }

    /**
     * Gets the cost to move from the begin node to this node
     * @return integer
     */
    public function getMovementCost() {
        return $this->movementCost;
    }

    /**
     * Sets the estimated cost from this node to the end node
     * @param integer $cost
     * @return null
     */
    public function setEstimatedMovementCost($cost) {
        $this->estimatedMovementCost = $cost;
        $this->calculateCost();
    }

    /**
     * Gets the estimated cost from this node to the end node
     * @return integer
     */
    public function getEstimatedMovementCost() {
        return $this->estimatedMovementCost;
    }

    /**
     * Gets the total cost of this node
     * @return integer
     */
    public function getCost() {
        return $this->cost;
    }

    /**
     * Calculates the cost
     * @return null
     */
    private function calculateCost() {
        $this->cost = $this->movementCost + $this->estimatedMovementCost;
    }

    /**
     * Checks if this node points to the same point as the provided node
     * @return boolean True if both nodes have the same point, false otherwise
     */
    public function equals(Node $node) {
        return $this->point->equals($node->point);
    }

}