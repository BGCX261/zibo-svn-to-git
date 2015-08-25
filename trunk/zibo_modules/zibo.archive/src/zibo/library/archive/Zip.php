<?php

namespace zibo\library\archive;

use zibo\library\archive\exception\ArchiveException;
use zibo\library\filesystem\File;

use \ZipArchive;

/**
 * Zip archive implementation
 */
class Zip extends AbstractArchive {

    /**
     * Constructs a new zip archive
     * @param zibo\library\filesystem\File $file The file of the archive
     * @return null
     * @throws zibo\library\archive\exception\ArchiveException when the zip extension is not installed
     */
    public function __construct(File $file) {
        if (!class_exists('ZipArchive')) {
            throw new ArchiveException('Zip is unsupported on this system');
        }

        parent::__construct($file);
    }

    /**
     * Compresses a file or combination of files in the archive
     * @param array|zibo\library\filesystem\File $source File objects of the files to compress
     * @param zibo\library\filesystem\File $prefix The path for the files in the archive
     * @return null
     * @throws zibo\library\archive\exception\ArchiveException when no source or an invalid source has been provided
     * @throws zibo\library\archive\exception\ArchiveException when the archive could not be created
     */
    public function compress($source, File $prefix = null) {
        if (empty($source)) {
            throw new ArchiveException('No files provided');
        }

        $path = $this->file->getAbsolutePath();

        $parent = $this->file->getParent();
        $parent->create();

        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::CREATE) !== true) {
            throw new ArchiveException('Could not create ' . $path);
        }

        if (!is_array($source)) {
            $source = array($source);
        }

        foreach ($source as $file) {
            if (!($file instanceof File)) {
                throw new ArchiveException('Invalid source provided: ' . $file);
            }
            $this->compressFile($zip, $file, $prefix);
        }

        $zip->close();
    }

    /**
     * Compresses a file into the archive
     * @param ZipArchive $archive ZipArchive object of PHP
     * @param zibo\library\filesystem\File $file The file to compress in the archive
     * @param zibo\library\filesystem\File $prefix The path for the file in the archive
     * @return null
     */
    private function compressFile(ZipArchive $archive, File $file, File $prefix = null) {
        if ($prefix == null) {
            $prefix = new File($file->getName());
        } else {
            $prefix = new File($prefix, $file->getName());
        }

        $children = null;

        if ($file->exists()) {
            if ($file->isDirectory()) {
                $children = $file->read();
            } else {
                $archive->addFile($file->getPath(), $prefix->getPath());
                return;
            }
        }

        if (empty($children)) {
            $archive->addEmptyDir($prefix->getPath());
        } else {
            foreach ($children as $file) {
                $this->compressFile($archive, $file, $prefix);
            }
        }
    }

    /**
     * Uncompresses the archive to the provided destination
     * @param zibo\library\filesystem\File $destination Destination of the uncompressed files
     * @return null
     */
    public function uncompress(File $destination) {
        $path = $this->file->getAbsolutePath();

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new ArchiveException('Could not open ' . $path);
        }

        $destination->create();

        $zip->extractTo($destination->getAbsolutePath());
        $zip->close();
    }

}