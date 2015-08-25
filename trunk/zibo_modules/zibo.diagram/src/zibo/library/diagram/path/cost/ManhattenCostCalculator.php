<?php

namespace zibo\library\diagram\path\cost;

use zibo\library\diagram\path\Node;

/**
 * Estimated cost calculater according to the Manhatten principle
 */
class ManhattenCostCalculator implements CostCalculator {

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

        $cost += $this->getDifference($x1, $x2);
        $cost += $this->getDifference($y1, $y2);

        $cost *= $orthogonalCost;

        if ($x1 == $x2 || $y1 == $y2) {
            $cost -= round($orthogonalCost / 2);
        }

        return $cost;
    }

    /**
     * Gets the absolute difference between 2 points
     * @param integer $p1
     * @param integer $p2
     * @return integer
     */
    protected function getDifference($p1, $p2) {
        if ($p1 < $p2) {
            return $p2 - $p1;
        } else {
            return $p1 - $p2;
        }
    }

}