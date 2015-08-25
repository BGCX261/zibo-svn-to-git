<?php

namespace zibo\library\diagram\layer;

use zibo\library\diagram\Diagram;
use zibo\library\image\Color;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * Layer implementation to fill the occupied grid cells
 */
class GridOccupationLayer implements Layer {

    /**
     * The color of the occupied cells
     * @var zibo\library\image\Color
     */
    private $color;

    /**
     * Constructs a new grid occupation layer
     * @return null
     */
    public function __construct() {
        $this->color = new Color(100, 100, 100);
    }

    /**
     * Draws the layers content on the image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function draw(Image $image, Diagram $diagram) {
        $grid = $diagram->getGrid();

        $margin = $diagram->getMargin();

        $cellDimension = $grid->getCellDimension();
        $cellWidth = $cellDimension->getWidth();
        $cellHeight = $cellDimension->getHeight();

        $gridDimension = $grid->getGridDimension();
        $gridWidth = $gridDimension->getWidth();
        $gridHeight = $gridDimension->getHeight();

        for ($x = 0; $x <= $gridWidth; $x++) {
            for ($y = 0; $y <= $gridHeight; $y++) {
                if ($grid->isFree(new Point($x, $y))) {
                    continue;
                }

                $pointX = ($x * $cellWidth) + $margin;
                $pointY = ($y * $cellHeight) + $margin;
                $point = new Point($pointX, $pointY);

                $image->fillRectangle($point, $cellDimension, $this->color);
            }
        }
    }

}