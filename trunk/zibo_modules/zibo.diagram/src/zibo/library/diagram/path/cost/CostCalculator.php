<?php

namespace zibo\library\diagram\path\cost;

use zibo\library\diagram\path\Node;

/**
 * Interface to calculate the cost between 2 nodes
 */
interface CostCalculator {

    /**
     * Calculates the cost between 2 nodes
     * @param zibo\library\diagram\path\Node $node1 The first node
     * @param zibo\library\diagram\path\Node $node2 The second node
     * @param integer $orthogonalCost Cost for a non-diagonal move
     * @param integer $diagonalCost Cost for a diagonal move
     * @return integer
     */
    public function calculateCost(Node $node1, Node $node2, $orthogonalCost, $diagonalCost);

}