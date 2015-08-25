<?php

namespace zibo\library\image\io;

use zibo\library\filesystem\File;

/**
 * Image input output interface
 */
interface ImageIO {

    /**
     * Read an image from file
     * @param File file of the image
     * @return resource internal PHP image resource of the file
     */
    public function read(File $file);

    /**
     * Write an image to file
     * @param File file of the image
     * @param resource internal PHP image resource
     */
    public function write(File $file, $resource);

}