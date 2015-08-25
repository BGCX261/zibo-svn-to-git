<?php

namespace zibo\library\image;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ImageException;
use zibo\library\image\io\GifImageIO;
use zibo\library\image\io\ImageIO;
use zibo\library\image\io\JpgImageIO;
use zibo\library\image\io\PngImageIO;
use zibo\library\String;

/**
 * IO facade for the image object: reads and writes image resources
 */
class ImageFactory {

    /**
     * Instance of this factory (singleton)
     * @var ImageFactory
     */
    private static $instance;

    /**
     * Array with the available image IO's
     * @var array
     */
    private $io;

    /**
     * Constructs a new image factory
     * @return null
     */
    private function __construct() {
        $jpgIO = new JpgImageIO();

        $this->io = array(
            'gif' => new GifImageIO(),
            'jpeg' => $jpgIO,
            'jpg' => $jpgIO,
            'png' => new PngImageIO(),
        );
    }

    /**
     * Get the instance of the factory
     * @return ImageFactory instance of the factory
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Read an image from file
     * @param File file of the image to read
     * @return resource internal image resource of the read image
     */
    public function read(File $file) {
        $io = $this->getImageIO($file);
        return $io->read($file);
    }

    /**
     * Write an image to file
     * @param File file of the image to write
     * @param resource internal image resource to be written
     */
    public function write(File $file, $resource) {
        $io = $this->getImageIO($file);

        $parent = $file->getParent();
        $parent->create();

        $io->write($file, $resource);
    }

    /**
     * Register an ImageIO to this factory
     * @param string extension extension of the images the IO will handle
     * @param ImageIO ImageIO for this extension
     */
    public function register($extension, ImageIO $io) {
        if (String::isEmpty($extension)) {
            throw new ImageException('extension is empty');
        }
        $this->io[$extension] = $io;
    }

    /**
     * Get the ImageIO for the given file. ImageIO choice is based on the extension of the file.
     * @param File file to get the ImageIO for
     */
    private function getImageIO(File $file) {
        $extension = $file->getExtension();

        if (!isset($this->io[$extension])) {
            throw new ImageException($extension . ' is not supported (' . $file->getPath() . ')');
        }

        return $this->io[$extension];
    }

}