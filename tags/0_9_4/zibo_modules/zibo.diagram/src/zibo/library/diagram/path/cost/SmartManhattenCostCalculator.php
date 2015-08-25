<?php

namespace zibo\library\diagram\path\cost;

use zibo\library\diagram\path\Node;

/**
 * Estimated cost calculater according to the Manhatten principle
 */
class SmartManhattenCostCalculator extends ManhattenCostCalculator {

    /**
     * Calculates the cost between 2 nodes
     * @param zibo\library\diagram\path\Node $node1 The first node
     * @param zibo\library\diagram\path\Node $node2 The second node
     * @param integer $orthogonalCost Cost for a non-diagonal move
     * @param integer $diagonalCost Cost for a diagonal move
     * @return integer
     */
    public function calculateCost(Node $node1, Node $node2, $orthogonalCost, $diagonalCost) {
        $p1 = $node1->getPoint();
        $x1 = $p1->getX();
        $y1 = $p1->getY();

        $p2 = $node2->getPoint();
        $x2 = $p2->getX();
        $y2 = $p2->getY();

        $cost = 0;

        if ($x1 == $x2 && $y1 == $y2) {
            return $cost;
        }

        $diffX = $this->getDifference($x1, $x2);
        $diffY = $this->getDifference($y1, $y2);

        $min = min($diffX, $diffY);
        $max = max($diffX, $diffY);

        $cost += $min * $diagonalCost;
        $cost += ($max - $min) * $orthogonalCost;

        return $cost;
    }

}