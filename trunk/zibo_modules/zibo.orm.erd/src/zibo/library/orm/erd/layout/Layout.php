<?php

namespace zibo\library\orm\erd\layout;

use zibo\library\diagram\Grid;

/**
 * Interface to perform the layouting of the model diagram objects on the diagram grid
 */
interface Layout {

    /**
     * Performs the layouting of the diagram objects
     * @param zibo\library\diagram\Grid $grid The grid to place the objects on
     * @param array $diagramObjects An array with the model diagram objects to layout
     * @return null
     */
    public function performLayout(Grid $grid, array $diagramObjects);

}