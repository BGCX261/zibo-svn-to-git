<?php

namespace zibo\library\diagram\layer;

use zibo\library\diagram\Diagram;
use zibo\library\image\Image;
use zibo\library\image\Point;

/**
 * Layer implementation to draw the diagram objects
 */
class ObjectLayer implements Layer {

    /**
     * Draws the layers content on the image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function draw(Image $image, Diagram $diagram) {
        $grid = $diagram->getGrid();

        $cellDimension = $grid->getCellDimension();

        $margin = $diagram->getMargin();
        $cellWidth = $cellDimension->getWidth();
        $cellHeight = $cellDimension->getHeight();

        $objects = $grid->getDiagramObjects();
        foreach ($objects as $object) {
            $gridPoint = $object->getGridPoint();

            $drawX = ($gridPoint->getX() * $cellWidth) + $margin;
            $drawY = ($gridPoint->getY() * $cellHeight) + $margin;
            $drawPoint = new Point($drawX, $drawY);

            $object->draw($image, $drawPoint);
        }
    }

}