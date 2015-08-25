<?php

namespace zibo\library\diagram\layer;

use zibo\library\diagram\Diagram;
use zibo\library\image\Image;

/**
 * Interface of a drawing layer
 */
interface Layer {

    /**
     * Draws the layers content on the image
     * @param zibo\library\image\Image $image The image to draw upon
     * @param zibo\library\diagram\Diagram $diagram The diagram we're drawing
     * @return null
     */
    public function draw(Image $image, Diagram $diagram);

}