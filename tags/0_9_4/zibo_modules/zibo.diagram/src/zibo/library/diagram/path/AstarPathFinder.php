<?php

namespace zibo\library\diagram\path;

use zibo\library\diagram\path\cost\DirectionCostCalculator;
use zibo\library\diagram\path\cost\SmartManhattenCostCalculator;
use zibo\library\diagram\Diagram;
use zibo\library\diagram\Grid;
use zibo\library\image\Point;

/**
 * Path finder using the A* algorithm
 */
class AstarPathFinder extends AbstractPathFinder {

    /**
     * Cost calculator for the direction cost
     * @var zibo\library\diagram\path\cost\CostCalculator
     */
    protected $directionCostCalculator;

    /**
     * Cost calculator to calculate the estimated movement cost
     * @var zibo\library\diagram\path\cost\CostCalculator
     */
    protected $estimatedCostCalculator;

    /**
     * The cost to move 1 node in a horizontal or vertical direction
     * @var integer
     */
    protected $orthogonalCost;

    /**
     * The cost to move 1 node in a diagonal direction
     * @var integer
     */
    protected $diagonalCost;

    /**
     * Lowest possible X value in the grid
     * @var integer
     */
    protected $gridMinX;

    /**
     * Highest possible X value in the grid
     * @var integer
     */
    protected $gridMaxX;

    /**
     * Lowest possible Y value in the grid
     * @var integer
     */
    protected $gridMinY;

    /**
     * Highest possible Y value in the grid
     * @var integer
     */
    protected $gridMaxY;

    /**
     * The list with nodes to check
     * @var array
     */
    protected $opened;

    /**
     * The list with checked nodes
     * @var array
     */
    protected $closed;

    /**
     * Construct a new A* path finder
     * @return null
     */
    public function __construct() {
        $this->directionCostCalculator = new DirectionCostCalculator();
        $this->estimatedCostCalculator = new SmartManhattenCostCalculator();

        $this->orthogonalCost = 10;
        $this->diagonalCost = 14;
    }

    /**
     * Sets the diagram for the finder
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function setDiagram(Diagram $diagram) {
        parent::setDiagram($diagram);

        $dimension = $this->grid->getGridDimension();

        $this->gridMinX = -1;
        $this->gridMinY = -1;
        $this->gridMaxX = $dimension->getWidth() + 1;
        $this->gridMaxY = $dimension->getHeight() + 1;
    }

    /**
     * Finds a path for the connection
     * @param zibo\library\image\Point $begin The begin point in the grid
     * @param zibo\library\image\Point $end The end point in the grid
     * @return false|array An array of points to follow in order to draw the connection if found, false otherwise
     */
    public function findPath(Point $begin, Point $end) {
        if (!$this->grid->isFree($begin)) {
            return false;
        }
        if (!$this->grid->isFree($end)) {
            return false;
        }

        if ($begin->equals($end)) {
            return array($begin, $end);
        }

        $beginNode = new Node($begin);
        $endNode = new Node($end);

        $path = $this->performAlgorithm($beginNode, $endNode);

        if ($path === false) {
            return false;
        }

        unset($this->opened);
        unset($this->closed);

        return $path;
    }

    /**
     * Performs the A* algorithm
     * @param Node $beginNode The begin node
     * @param Node $endNode The end node
     * @return boolean True if a path has been found, false otherwise
     */
    protected function performAlgorithm(Node $beginNode, Node $endNode) {
        $this->opened = array();
        $this->closed = array();

        $this->addNodeToOpened($beginNode);

        do {
            $currentNode = $this->getNextNode();

            $this->addNodeToClosed($currentNode);

            $surroundingNodes = $this->getSurroundingNodes($currentNode);
            foreach ($surroundingNodes as $surroundingNode) {
                $openNode = $this->getNodeFromOpened($surroundingNode);
                if ($openNode) {
                    $movementCost = $this->calculateMovementCost($currentNode, $openNode);

                    if ($movementCost < $openNode->getMovementCost()) {
                        $openNode->setParent($currentNode->getId());
                        $openNode->setMovementCost($movementCost);
                    }
                } else {
                    $surroundingNode->setParent($currentNode->getId());

                    if ($surroundingNode->equals($endNode)) {
                        return $this->getPathFromNode($surroundingNode);
                    }

                    $movementCost = $this->calculateMovementCost($currentNode, $surroundingNode);
                    $estimatedMovementCost = $this->calculateEstimatedMovementCost($surroundingNode, $endNode);

                    $surroundingNode->setMovementCost($movementCost);
                    $surroundingNode->setEstimatedMovementCost($estimatedMovementCost);

                    $this->addNodeToOpened($surroundingNode);
                }
            }
        } while ($this->opened);

        return false;
    }

    /**
     * Gets the traversed path from the provided node
     * @param Node $node
     * @return array Array of traversed points
     */
    protected function getPathFromNode(Node $node) {
        $path = array();

        while ($node) {
            $path[] = $node->getPoint();

            $parentId = $node->getParent();

            if (array_key_exists($parentId, $this->closed)) {
                $node = $this->closed[$parentId];
            } else {
                $node = null;
            }
        }

        return array_reverse($path);
    }

    /**
     * Gets the next node from the opened list for the algorithm. This node must have the lowest cost in the opened list
     * @return Node
     */
    protected function getNextNode() {
        $minCost = null;
        $minIds = array();

        foreach ($this->opened as $node) {
            $cost = $node->getCost();

            if ($minCost === null) {
                $minCost = $cost;
                $minIds[] = $node->getId();
                continue;
            }

            if ($cost == $minCost) {
                $minIds[] = $node->getId();
            } elseif ($cost < $minCost) {
                $minCost = $cost;
                $minIds = array($node->getId());
            }
        }

        if (!$minIds) {
            return null;
        }

        $nodeId = array_pop($minIds);
        if (count($minIds) == 0) {
            return $this->opened[$nodeId];
        }

        $alternative = $minIds;

        $minCost = null;
        $minId;

        foreach ($alternative as $nodeId) {
            $node = $this->opened[$nodeId];

            $cost = $node->getEstimatedMovementCost();

            if ($minCost === null) {
                $minCost = $cost;
                $minId = $nodeId;
                continue;
            }

            if ($cost < $minCost) {
                $minCost = $cost;
                $minId = $nodeId;
            }
        }

        return $this->opened[$minId];
    }

    /**
     * Gets the surrounding nodes of the provided Node.
     * @param Node $node
     * @return array Surrounding nodeswhich are free in the grid and not on the closed list
     */
    protected function getSurroundingNodes(Node $node) {
        $thresholdCost = 3 * $this->orthogonalCost;
        $movementCost = $node->getMovementCost();
        $estimatedMovementCost = $node->getEstimatedMovementCost();

        if ($movementCost < $thresholdCost || $estimatedMovementCost < $thresholdCost) {
            $threshold = 0;
        } elseif ($movementCost < $thresholdCost * 5 || $estimatedMovementCost < $thresholdCost * 5) {
            $threshold = 1;
        } else {
            $threshold = 2;
        }

        $x = $node->getPoint()->getX();
        $y = $node->getPoint()->getY();

        $minX = min(max($this->gridMinX, $x - 1), $this->gridMaxX);
        $maxX = min(max($this->gridMinX, $x + 1), $this->gridMaxX);
        $minY = min(max($this->gridMinY, $y - 1), $this->gridMaxY);
        $maxY = min(max($this->gridMinY, $y + 1), $this->gridMaxY);

        $surroundingNodes = array();

        for ($i = $minX; $i <= $maxX; $i++) {
            for ($j = $minY; $j <= $maxY; $j ++) {
                if ($i == $x && $j == $y) {
                    continue;
                }

                $point = new Point($i, $j);
                $node = new Node($point);
                $id = $node->getId();

                if (array_key_exists($id, $this->closed)) {
                    continue;
                }

                if (!$this->grid->isFree($point, $threshold)) {
                    continue;
                }

                $surroundingNodes[$id] = $node;
            }
        }

        return $surroundingNodes;
    }

    /**
     * Calculates the movement cost between 2 nearby nodes
     * @param Node $nodeBegin Begin node of the movement
     * @param Node $nodeEnd End node of the movement
     * @return integer Movement cost
     */
    protected function calculateMovementCost(Node $nodeBegin, Node $nodeEnd) {
        $cost = $this->directionCostCalculator->calculateCost($nodeBegin, $nodeEnd, $this->orthogonalCost, $this->diagonalCost);
        return $nodeBegin->getMovementCost() + $cost;
    }

    /**
     * Calculates the estimated movement cost between 2 nodes
     * @param Node $nodeBegin Begin node of the movement
     * @param Node $nodeEnd End node of the movement
     * @return integer Estimated movement cost
     */
    protected function calculateEstimatedMovementCost(Node $nodeBegin, Node $nodeEnd) {
        return $this->estimatedCostCalculator->calculateCost($nodeBegin, $nodeEnd, $this->orthogonalCost, $this->diagonalCost);
    }

    /**
     * Gets a node from the opened list
     * @param Node $node Node to get from the opened list
     * @retrun boolean|Node The node from the opened list if found, false otherwise
     */
    protected function getNodeFromOpened(Node $node) {
        $id = $node->getId();

        if (!array_key_exists($id, $this->opened)) {
            return false;
        }

        return $this->opened[$id];
    }

    /**
     * Adds a node to the opened list
     * @param Node $node Node to add to the opened list
     * @return null
     */
    protected function addNodeToOpened(Node $node) {
        $this->opened[$node->getId()] = $node;
    }

    /**
     * Adds a node to the closed list and removed it from the opened list
     * @param Node $node Node to add to the closed list
     * @return null
     */
    protected function addNodeToClosed(Node $node) {
        $id = $node->getId();

        $this->closed[$id] = $node;
        unset($this->opened[$id]);
    }

}