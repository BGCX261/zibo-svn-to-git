<?php

namespace zibo\library\image\thumbnail;

use zibo\library\image\Image;

/**
 * Thumbnailer using the crop method
 */
class CropThumbnailer extends AbstractThumbnailer {

    /**
     * Get a cropped thumbnail from the given image
     * @param Image image source image for the thumbnail
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image Image instance of the thumbnail
     */
    protected function createThumbnail(Image $image, $thumbnailWidth, $thumbnailHeight) {
        $x = 0;
        $y = 0;
        $width = $thumbnailWidth;
        $height = $thumbnailHeight;

        if ($image->getWidth() > $image->getHeight()) {
            $this->calculateNewSize($image->getWidth(), $image->getHeight(), $width, $height, $x, $y);
        } else {
            $this->calculateNewSize($image->getHeight(), $image->getWidth(), $height, $width, $y, $x);
        }

        $thumbnail = $image->resize($width, $height);
        $thumbnail = $thumbnail->crop($x, $y, $thumbnailWidth, $thumbnailHeight);

        return $thumbnail;
    }

    /**
     * Calculate the new sizes
     * @param int originalA original size A
     * @param int originalB original size B
     * @param int thumbnailA maximum thumbnail size A
     * @param int thumbnailB maximum thumbnail size B
     * @param int startA start position for A
     * @param int startB start position for B
     */
    private function calculateNewSize($originalA, $originalB, &$thumbnailA, &$thumbnailB, &$startA, &$startB) {
        $difference = $originalA / $thumbnailA;
        $newA = $thumbnailA;
        $newB = round($originalB / $difference);

        if ($newB < $thumbnailB) {
            $difference = $originalB / $thumbnailB;
            $newA = round($originalA / $difference);
            $newB = $thumbnailB;
            $startA = floor(($newA - $thumbnailA) / 2);
        } else {
            $startB = floor(($newB - $thumbnailB) / 2);
        }

        $thumbnailA = $newA;
        $thumbnailB = $newB;
    }

}