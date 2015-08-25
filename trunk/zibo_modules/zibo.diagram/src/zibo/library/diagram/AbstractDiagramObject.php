<?php

namespace zibo\library\diagram;

use zibo\library\image\Dimension;
use zibo\library\image\Point;

/**
 * Abstract implementation of a diagram object
 */
abstract class AbstractDiagramObject implements DiagramObject {

    /**
     * Unique id for this object
     * @var string
     */
    protected $id;

    /**
     * The dimension of this object in picels
     * @var zibo\library\image\Dimension
     */
    protected $dimension;

    /**
     * The grid dimension
     * @var zibo\library\image\Dimension
     */
    protected $gridDimension;

    /**
     * The grid point
     * @var zibo\library\image\Point
     */
    protected $gridPoint;

    /**
     * Gets a unique id for the object
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Gets the dimension of the object in pixels
     * @return zibo\library\image\Dimension
     */
    public function getDimension() {
        return $this->dimension;
    }

    /**
     * Gets the dimension of the object in cells on the grid
     * @return zibo\library\image\Dimension
     */
    public function getGridDimension() {
        return $this->gridDimension;
    }

    /**
     * Sets the dimension of the object in cells on the grid
     * @param zibo\library\image\Dimension $dimension
     * @return null
     */
    public function setGridDimension(Dimension $dimension = null) {
        $this->gridDimension = $dimension;
    }

    /**
     * Gets the top left point in the grid for this object
     * @return zibo\library\image\Point
     */
    public function getGridPoint() {
        return $this->gridPoint;
    }

    /**
     * Sets the top left point in the grid for this object
     * @param zibo\library\image\Point $point
     * @return null
     */
    public function setGridPoint(Point $point = null) {
        $this->gridPoint = $point;
    }

}