<?php

namespace zibo\library\image\io;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;

/**
 * GIF image input/output implementation
 */
class GifImageIO extends AbstractImageIO {

    /**
     * Read a gif image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file) {
        $this->checkIfReadIsPossible($file, 'gif');

        $image = imageCreateFromGif($file->getAbsolutePath());
        if ($image === false) {
            throw new ImageException($file->getPath() . ' is not a valid GIF image');
        }

        return $image;
    }

    /**
     * Write a gif image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource) {
        $this->checkIfWriteIsPossible($file, $resource, 'gif');

        imageGif($resource, $file->getAbsolutePath());
    }

}