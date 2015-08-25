<?php

namespace zibo\library\diagram\path;

use zibo\library\diagram\Diagram;
use zibo\library\image\Point;

/**
 * Interface to find a path for a diagram connection
 */
interface PathFinder {

    /**
     * Sets the diagram for the finder
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function setDiagram(Diagram $diagram);

    /**
     * Finds a path for the connection
     * @param zibo\library\image\Point $begin The begin point in the grid
     * @param zibo\library\image\Point $end The end point in the grid
     * @return false|array An array of points to follow in order to draw the connection if found, false otherwise
     */
    public function findPath(Point $begin, Point $end);

}