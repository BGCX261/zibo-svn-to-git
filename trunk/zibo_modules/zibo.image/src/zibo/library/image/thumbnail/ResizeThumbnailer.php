<?php

namespace zibo\library\image\thumbnail;

use zibo\library\image\Image;

/**
 * Thumbnailer using the resize method
 */
class ResizeThumbnailer extends AbstractThumbnailer {

    /**
     * Get a resized thumbnail from the given image
     * @param Image image source image for the thumbnail
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    protected function createThumbnail(Image $image, $width, $height) {
        $imageWidth = $image->getWidth();
        $imageHeight = $image->getHeight();

        if ($imageWidth > $imageHeight) {
            $this->calculateNewSize($imageWidth, $imageHeight, $width, $height);
        } else {
            $this->calculateNewSize($imageHeight, $imageWidth, $height, $width);
        }

        return $image->resize($width, $height);
    }

    /**
     * Calculate the new sizes
     * @param int originalA original size A
     * @param int originalB original size B
     * @param int thumbnailA maximum thumbnail size A
     * @param int thumbnailB maximum thumbnail size B
     */
    private function calculateNewSize($originalA, $originalB, &$thumbnailA, &$thumbnailB) {
        $ratio = $originalA / $thumbnailA;
        $newA = $thumbnailA;
        $newB = round($originalB / $ratio);

        if ($newB > $thumbnailB) {
            $ratio = $originalB / $thumbnailB;
            $newA = round($originalA / $ratio);
            $newB = $thumbnailB;
        }

        $thumbnailA = $newA;
        $thumbnailB = $newB;
    }

}