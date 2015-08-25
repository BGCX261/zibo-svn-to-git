<?php

namespace zibo\library\diagram;

use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * A connection between 2 objects on the grid
 */
interface DiagramConnection {

    /**
     * Gets a unique id for the connection
     * @return string
     */
    public function getId();

    /**
     * Gets the begin of the connection
     * @return string|zibo\library\image\Point Id of the begin diagram object or a point in the grid
     */
    public function getBegin();

    /**
     * Gets the end of the connection
     * @return string|zibo\library\image\Point Id of the end diagram object or a point in the grid
     */
    public function getEnd();

    /**
     * Sets the points to connect in order to draw this connection
     * @param array $points Array of Point objects in the image
     * @return null
     */
    public function setPoints(array $points);

    /**
     * Gets the points to connect in order to draw this connection
     * @return array Array of Point objects in the image
     */
    public function getPoints();

    /**
     * Hook to process the connection before the connection layer is drawn
     * @param zibo\library\diagram\Diagram $diagram
     * @return null
     */
    public function preDraw(Diagram $diagram);

    /**
     * Draw the connection on the provided image
     * @param zibo\library\image\Image $image
     * @return null
     */
    public function draw(Image $image);

}