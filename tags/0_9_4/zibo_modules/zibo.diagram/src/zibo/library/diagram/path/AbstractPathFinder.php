<?php

namespace zibo\library\diagram\path;

use zibo\library\diagram\Diagram;

/**
 * Abstract implementation of a path finder
 */
abstract class AbstractPathFinder implements PathFinder {

    /**
     * The diagram
     * @var zibo\library\diagram\Diagram
     */
    protected $diagram;

    /**
     * The grid of the diagram
     * @var zibo\library\diagram\Grid
     */
    protected $grid;

    /**
     * Sets the diagram for the finder
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function setDiagram(Diagram $diagram) {
        $this->diagram = $diagram;
        $this->grid = $diagram->getGrid();
    }

}