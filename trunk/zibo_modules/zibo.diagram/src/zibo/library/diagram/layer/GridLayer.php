<?php

namespace zibo\library\diagram\layer;

use zibo\library\diagram\Diagram;
use zibo\library\image\Color;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * Layer implementation to draw the grid cells
 */
class GridLayer implements Layer {

    /**
     * The color of the grid
     * @var zibo\library\image\Color
     */
    private $gridColor;

    /**
     * The color of the helper
     * @var zibo\library\image\Color
     */
    private $helperColor;

    /**
     * Number of cells between every usage of the helper color
     * @var integer
     */
    private $helperLocation;

    /**
     * Constructs a new grid layer
     * @return null
     */
    public function __construct() {
        $this->gridColor = new Color(231, 231, 231);
        $this->helperColor = new Color(207, 207, 207);
        $this->helperLocation = 10;
    }

    /**
     * Draws the layers content on the image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function draw(Image $image, Diagram $diagram) {
        $grid = $diagram->getGrid();

        $width = $image->getWidth();
        $height = $image->getHeight();

        $cellDimension = $grid->getCellDimension();

        $margin = $diagram->getMargin();
        $cellWidth = $cellDimension->getWidth();
        $cellHeight = $cellDimension->getHeight();

        // take margin into account and go out of the image to start the grid
        $loopX = $loopY = $this->helperLocation - 1;
        $x = $margin;
        while ($x > 0) {
            $x -= $cellWidth;
            $loopX--;
        }

        $y = $margin;
        while ($y > 0) {
            $y -= $cellHeight;
            $loopY--;
        }

        // draw the X-axis
        for (; $x < $width; $x += $cellWidth) {
            $loopX++;
            if ($loopX == $this->helperLocation) {
                $color = $this->helperColor;
                $loopX = 0;
            } else {
                $color = $this->gridColor;
            }

            $image->drawLine(new Point($x, 0), new Point($x, $height), $color);
        }

        // draw the Y-axis
        for (; $y < $height; $y += $cellHeight) {
            $loopY++;
            if ($loopY == $this->helperLocation) {
                $color = $this->helperColor;
                $loopY = 0;
            } else {
                $color = $this->gridColor;
            }

            $image->drawLine(new Point(0, $y), new Point($width, $y), $color);
        }
    }

}