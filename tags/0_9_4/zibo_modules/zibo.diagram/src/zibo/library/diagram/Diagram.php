<?php

namespace zibo\library\diagram;

use zibo\library\diagram\layer\Layer;
use zibo\library\image\Color;
use zibo\library\image\Dimension;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * The main object to draw a diagram
 */
class Diagram {

    /**
     * The grid of the diagram
     * @var Grid
     */
    private $grid;

    /**
     * Array with drawing layers
     * @var array
     */
    private $layers;

    /**
     * The background color of the diagram
     * @var zibo\library\image\Color
     */
    private $backgroundColor;

    /**
     * The margin around the diagram in pixels
     * @var integer
     */
    private $margin;

    /**
     * Constructs a new diagram
     * @return null
     */
    public function __construct() {
        $this->grid = new Grid();
        $this->layers = array();
        $this->backgroundColor = new Color(255, 255, 255);
        $this->margin = 50;
    }

    /**
     * Gets the grid of the diagram
     * @return Grid
     */
    public function getGrid() {
        return $this->grid;
    }

    /**
     * Gets the margin of the diagram
     * @return integer The margin of the diagram in pixels
     */
    public function getMargin() {
        return $this->margin;
    }

    /**
     * Sets the margin of the diagram
     * @param integer $margin The margin of the diagram in pixels
     */
    public function setMargin($margin) {
        $this->margin = $margin;
    }

    /**
     * Adds a drawing layer to the diagram
     * @param zibo\library\diagram\layer\Layer $layer
     * @return null
     */
    public function addLayer(Layer $layer) {
        $this->layers[] = $layer;
    }

    /**
     * Creates the image of this diagram
     * @return zibo\library\image\Image
     */
    public function getImage() {
        $image = $this->createImage();

        $this->drawLayers($image);

        return $image;
    }

    /**
     * Creates the image needed to draw the layers out of the grid
     * @return zibo\library\image\Image
     */
    private function createImage() {
        $gridDimension = $this->grid->getGridDimension();
        $cellDimension = $this->grid->getCellDimension();

        $margin = 2 * $this->margin;

        $width = ($gridDimension->getWidth() * $cellDimension->getWidth()) + $margin;
        $height = ($gridDimension->getHeight() * $cellDimension->getHeight()) + $margin;

        $image = new Image(null, $width, $height);
        $image->fillRectangle(new Point(0, 0), new Dimension($width, $height), $this->backgroundColor);

        return $image;
    }

    /**
     * Draw the layers on the image
     * @param zibo\library\image\Image $image The image to draw upon
     * @return null
     */
    private function drawLayers(Image $image) {
        foreach ($this->layers as $layer) {
            $layer->draw($image, $this);
        }
    }

}