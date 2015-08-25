<?php

namespace zibo\library\image\io;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;

/**
 * JPG image input/output implementation
 */
class JpgImageIO extends AbstractImageIO {

    /**
     * Array with the supported extensions of this ImageIO
     * @var array
     */
    private $supportedExtensions = array('jpg', 'jpeg');

    /**
     * Read a jpg image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file) {
        $this->checkIfReadIsPossible($file, $this->supportedExtensions);

        $image = imageCreateFromJpeg($file->getAbsolutePath());
        if ($image === false) {
            throw new ImageException($file->getPath() . ' is not a valid JPG image');
        }

        return $image;
    }

    /**
     * Write a jpg image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource) {
        $this->checkIfWriteIsPossible($file, $resource, $this->supportedExtensions);

        imageJpeg($resource, $file->getAbsolutePath());
    }

}