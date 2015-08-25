<?php

namespace zibo\library\image\io;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;

/**
 * Abstract image input output
 */
abstract class AbstractImageIO implements ImageIO {

    /**
     * Check if the write action is valid and supported by this ImageIO.
     * @param File file to check if read is possible
     * @param mixed supportedExtensions string of an extension or array with extension strings
     */
    protected function checkIfReadIsPossible(File $file, $supportedExtensions) {
        $this->checkIfFileIsSupported($file, $supportedExtensions);

        if (!$file->isReadable()) {
            throw new ImageException('Could not read ' . $file->getPath());
        }
    }

    /**
     * Check if the write action is valid and supported by this ImageIO.
     * @param File file to check if write is possible
     * @param resource internal image resource to write
     * @param mixed supportedExtensions string of an extension or array with extension strings
     */
    protected function checkIfWriteIsPossible(File $file, $resource, $supportedExtensions) {
        $this->checkIfFileIsSupported($file, $supportedExtensions);

        if (!$file->isWritable()) {
            throw new ImageException('Could not write ' . $file->getPath());
        }

        if ($resource == null) {
            throw new ImageException('Resource is null');
        }
    }

    /**
     * Check if a file is supported by this ImageIO. This check is based on the extension.
     * @param File file to check if it is supported
     * @param mixed supportedExtensions string of an extension or array with extension strings
     */
    protected function checkIfFileIsSupported(File $file, $supportedExtensions) {
        if (!$file->hasExtension($supportedExtensions)) {
            throw new ImageException($file->getPath() . ' is not supported');
        }

        return true;
    }

}