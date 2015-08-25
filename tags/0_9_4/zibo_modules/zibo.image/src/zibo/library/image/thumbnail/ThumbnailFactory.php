<?php

namespace zibo\library\image\thumbnail;

use zibo\library\filesystem\File;
use zibo\library\image\exception\ThumbnailException;
use zibo\library\image\Image;
use zibo\library\String;

/**
 * Thumbnail factory
 */
class ThumbnailFactory {

    /**
     * Name of the crop thumbnailer
     * @var string
     */
    const CROP = 'crop';

    /**
     * Name of the resize thumbnailer
     * @var string
     */
    const RESIZE = 'resize';

    /**
     * Instance of this factory (singleton)
     * @var ThumbnailFactory
     */
    private static $instance;

    /**
     * Array with the registered thumbnailers
     * @var array
     */
    private $thumbnailers;

    /**
     * Constructs a new thumbnail factory
     * @return null
     */
    private function __construct() {
        $this->thumbnailers = array(
            self::CROP => new CropThumbnailer(),
            self::RESIZE => new ResizeThumbnailer(),
        );
    }

    /**
     * Get the instance of the thumbnail factory
     * @return ThumbnailFactory instance of the thumbnail factory
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a new thumbnailer into this factory
     * @param string name name of the thumbnailer
     * @param Thumbnailer thumbnailer Thumbnailer instance
     */
    public function register($name, Thumbnailer $thumbnailer) {
        if (String::isEmpty($name)) {
            throw new ThumbnailException('Name is empty');
        }
        $this->thumbnailers[$name] = $thumbnailer;
    }

    /**
     * Create a thumbnail from an image
     * @param string name name of the thumbnailer
     * @param Image image image to get a thumbnail from
     * @param int width width to calculate the thumbnail's width
     * @param int height height to calculate the thumbnail's height
     * @return Image image instance of the thumbnail
     */
    public function getThumbnail($name, Image $image, $width, $height) {
        if (!isset($this->thumbnailers[$name])) {
            throw new ThumbnailException($name . ' is not supported');
        }

        return $this->thumbnailers[$name]->getThumbnail($image, $width, $height);
    }

}