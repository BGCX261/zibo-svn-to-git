<?php

namespace zibo\library\image\io;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;

/**
 * PNG image input/output implementation
 */
class PngImageIO extends AbstractImageIO {

    /**
     * Read a png image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file) {
        $this->checkIfReadIsPossible($file, 'png');

        $image = imageCreateFromPng($file->getAbsolutePath());
        if ($image === false) {
            throw new ImageException($file->getPath() . ' is not a valid PNG image');
        }

        return $image;
    }

    /**
     * Write a png image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource) {
        $this->checkIfWriteIsPossible($file, $resource, 'png');

        imagePng($resource, $file->getAbsolutePath());
    }

}