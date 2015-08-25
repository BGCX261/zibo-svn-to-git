<?php

namespace zibo\library\image\thumbnail;

use zibo\library\image\Image;
use zibo\library\Number;

/**
 * Abstract image thumbnailer
 */
abstract class AbstractThumbnailer implements Thumbnailer {

    /**
     * Get a thumbnail from the given image
     * @param Image image source image for the thumbnail
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    public function getThumbnail(Image $image, $thumbnailWidth, $thumbnailHeight) {
        if (Number::isNegative($thumbnailWidth)) {
            throw new ThumbnailException($thumbnailWidth . ' is an invalid width');
        }
        if (Number::isNegative($thumbnailHeight)) {
            throw new ThumbnailException($thumbnailHeight . ' is an invalid height');
        }

        if ($image->getWidth() <= $thumbnailWidth && $image->getHeight() <= $thumbnailHeight) {
            return $image;
        }

        return $this->createThumbnail($image, $thumbnailWidth, $thumbnailHeight);
    }

    /**
     * Create a thumbnail from the given image
     * @param Image image source image for the thumbnail
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    abstract protected function createThumbnail(Image $image, $thumbnailWidth, $thumbnailHeight);

}