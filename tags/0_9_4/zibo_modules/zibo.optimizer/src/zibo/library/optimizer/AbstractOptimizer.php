<?php

namespace zibo\library\optimizer;

use zibo\core\Zibo;

use zibo\library\filesystem\Browser;
use zibo\library\filesystem\File;
use zibo\library\Structure;

use zibo\ZiboException;

/**
 * Optimize an array of files into one file
 */
abstract class AbstractOptimizer {

    /**
     * Path where the files will be cached
     * @var zibo\library\filesystem\File
     */
    private $cachePath;

    /**
     * Return path for the optimized file
     * @var zibo\library\filesystem\File
     */
    private $returnPath;

    /**
     * Constructs a new optimizer
     * @param zibo\library\filesystem\File $cachePath Path in the cache directory for the optimizer
     * @return null
     */
    public function __construct(File $cachePath) {
        $this->returnPath = new File(Zibo::DIRECTORY_PUBLIC . File::DIRECTORY_SEPARATOR . Zibo::DIRECTORY_CACHE, $cachePath);
        $this->cachePath = new File(Zibo::DIRECTORY_APPLICATION, $this->returnPath);
    }

    /**
     * Optimizes an array of files into 1 file
     * @param array $fileNames Array with the file names
     * @return string File name of the optimized file
     */
    public function optimize(array $fileNames) {
        $files = $this->getFilesFromArray($fileNames);

        $optimizedFile = $this->getOptimizedFile($files);

        if ($this->isGenerateNecessairy($optimizedFile, $files)) {
            $this->generateOptimizedFile($optimizedFile, $files);
        }

        return $this->returnPath . File::DIRECTORY_SEPARATOR . $optimizedFile->getName();
    }

    /**
     * Gets the file objects for the file names
     * @param array $fileNames Array with the file names
     * @return array Array with the file name as key and the File objact as value
     */
    protected function getFilesFromArray(array $fileNames) {
        $files = array();

        $zibo = Zibo::getInstance();

        foreach ($fileNames as $fileName) {
            $file = $zibo->getFile($fileName);
            if (!$file) {
                continue;
            }

            $files[$fileName] = $file;
        }

        return $files;
    }

    /**
     * Gets the optimized file object for the provided files
     * @param array $files Array of file objects of the files to optimize
     * @return zibo\library\filesystem\File File object of the optimized file
     */
    protected function getOptimizedFile(array $files) {
        $fileName = $this->getOptimizedFileHash($files);
        $fileName .= '.' . $this->getExtension();
        return new File($this->cachePath, $fileName);
    }

    /**
     * Gets a hash for the provided files
     * @param array $files Array of file objects of the files to optimize
     * @return string MD5 hash of the file names
     */
    protected function getOptimizedFileHash(array $files) {
        $md5 = implode('-', array_keys($files));
        return md5($md5);
    }

    /**
     * Gets the extension of this optimizer
     * @return string
     */
    abstract protected function getExtension();

    /**
     * Gets whether a new generation of the optimized file is necessairy
     * @param zibo\library\filesystem\File $optimizedFile The file of the optimized source
     * @param array $files Array with File objects of the files to optimize
     * @return boolean True if a new generation is necessairy, false otherwise
     */
    private function isGenerateNecessairy(File $optimizedFile, array $files) {
        if (!$optimizedFile->exists()) {
            $parent = $optimizedFile->getParent();
            if (!$parent->exists()) {
                $parent->create();
            }
            return true;
        }

        $cacheTime = $optimizedFile->getModificationTime();
        foreach ($files as $file) {
            if ($file->getModificationTime() >= $cacheTime) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generates the optimized source file
     * @param zibo\library\filesystem\File $optimizedFile The file of the optimized source
     * @param array $files Array with File objects of the files to optimize
     * @return null
     */
    protected function generateOptimizedFile(File $optimizedFile, array $files) {
        $output = '';

        foreach ($files as $file) {
            $source = $file->read();
            $output .= $this->optimizeSource($source, $file);
        }

        $optimizedFile->write($output);
    }

    /**
     * Optimizes the provided source
     * @param string $source The source to optimize
     * @param zibo\library\filesystem\File $file The file of the source
     * @return string Optimized source
     */
    abstract protected function optimizeSource($source, File $file);

}