<?php

namespace zibo\library\diagram;

use zibo\library\image\Dimension;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * An object which can be placed on the grid
 */
interface DiagramObject {

    /**
     * Gets a unique id for the object
     * @return string
     */
    public function getId();

    /**
     * Gets the dimension of the object in pixels
     * @return zibo\library\image\Dimension
     */
    public function getDimension();

    /**
     * Gets the dimension of the object in cells on the grid
     * @return zibo\library\image\Dimension
     */
    public function getGridDimension();

    /**
     * Sets the dimension of the object in cells on the grid
     * @param zibo\library\image\Dimension $dimension
     * @return null
     */
    public function setGridDimension(Dimension $dimension = null);

    /**
     * Gets the top left point in the grid for this object
     * @return zibo\library\image\Point
     */
    public function getGridPoint();

    /**
     * Sets the top left point in the grid for this object
     * @param zibo\library\image\Point
     * @return null
     */
    public function setGridPoint(Point $point = null);

    /**
     * Draws the object on the provided image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\image\Point $point The top left corner to start drawing
     * @return null
     */
    public function draw(Image $image, Point $point);

}