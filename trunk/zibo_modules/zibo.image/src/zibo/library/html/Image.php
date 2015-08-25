<?php

namespace zibo\library\html;

use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\html\AbstractElement;
use zibo\library\image\thumbnail\ThumbnailFactory;
use zibo\library\image\Image as CoreImage;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Image html element
 */
class Image extends AbstractElement {

    /**
     * Name of the source attribute
     * @var string
     */
    const ATTRIBUTE_SRC = 'src';

    /**
     * Path to cache processed images
     * @var string
     */
    const CACHE_PATH = 'application/public/cache/images';

    /**
     * Name of the thumbnailer
     * @var string
     */
    private $thumbnailer;

    /**
     * Width for the thumbnailer
     * @var int
     */
    private $thumbnailWidth;

    /**
     * Height for the thumbnailer
     * @var int
     */
    private $thumbnailHeight;

    /**
     * Source attribute for this image element
     * @var string
     */
    private $source;

    /**
     * Construct a image tag
     * @param string $source source of the image
     * @return null
     */
    public function __construct($source) {
        $this->setSource($source);
    }

    /**
     * Set an attribute to this image tag
     * @param string $attribute name of the attribute
     * @param string $value value for the attribute
     * @return null
     */
    public function setAttribute($attribute, $value) {
        if ($attribute == self::ATTRIBUTE_SRC) {
            $this->setSource($value);
        }
        parent::setAttribute($attribute, $value);
    }

    /**
     * Get an attribute value from this image tag
     * @param string $attribute name of the attribute
     * @param mixed $default default value for when the attribute is not set (default null)
     * @return mixed value of the attribute if set, default value otherwise
     */
    public function getAttribute($attribute, $default = null) {
        if ($attribute == self::ATTRIBUTE_SRC) {
            return $this->getSource();
        }
        return parent::setAttribute($attribute, $default);
    }

    /**
     * Set the source attribute for this image tag
     * @param string $source source for this image tag
     * @return null
     * @throws zibo\ZiboException when the source is empty or not a string
     */
    public function setSource($source) {
        if (String::isEmpty($source)) {
            throw new ZiboException('Empty source provided');
        }
        $this->source = $source;
    }

    /**
     * Get the source attribute of this image tag
     * @return string source for this image tag
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * Set a thumbnailer for this image
     * @param string $name name of the thumbnailer
     * @param int $width width for the thumbnailer
     * @param int $height height for the thumbnailer
     * @return null
     */
    public function setThumbnailer($name, $width, $height) {
        $this->thumbnailer = $name;
        $this->thumbnailWidth = $width;
        $this->thumbnailHeight = $height;
    }

    /**
     * Get the html for this image tag
     * @return string html of this image tag
     */
    public function getHtml() {
        $source = $this->getSource();

        if (!String::looksLikeUrl($source)) {
            $source = $this->processSource($source);

            $request = Zibo::getInstance()->getRequest();
            $source = $request->getBaseUrl() . '/' . $source;

            $this->setSource($source);
        }

        $html = '<img' .
            $this->getAttributeHtml(self::ATTRIBUTE_SRC, $source) .
            $this->getIdHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() . ' />';

        return $html;
    }

    /**
     * Process the source, apply thumbnailer if set
     * @param string $source source to process
     * @return string source to be used by the html of this image tag
     * @throws zibo\ZiboException when the source file could not be found
     */
    private function processSource($source) {
        $fileSource = new File($source);

        if (!$fileSource->isAbsolute() && !String::startsWith($fileSource->getPath(), Zibo::DIRECTORY_APPLICATION . File::DIRECTORY_SEPARATOR)) {
            $fileSource = Zibo::getInstance()->getFile($fileSource->getPath());

            if (!$fileSource) {
                throw new ZiboException('Could not find ' . $source);
            }
        }

        $fileDestination = $this->getCacheFile($fileSource);

        if (!$fileDestination->exists() || $fileSource->getModificationTime() > $fileDestination->getModificationTime()) {
            $image = new CoreImage($fileSource);
            if ($this->thumbnailer) {
                $thumbnail = $image->thumbnail($this->thumbnailer, $this->thumbnailWidth, $this->thumbnailHeight);
                if ($image === $thumbnail) {
                    $fileSource->copy($fileDestination);
                } else {
                    $thumbnail->write($fileDestination);
                }
            } else {
                $fileSource->copy($fileDestination);
            }
        }

        // remove application/ from the path
        return substr($fileDestination->getPath(), 12);
    }

    /**
     * Get the cache file for the image source
     * @param zibo\library\filesystem\File $source image source to get a cache file for
     * @return zibo\library\filesystem\File unique name for a source file, in the cache directory, with the thumbnailer, width and height encoded into
     */
    private function getCacheFile(File $source) {
        $filename = md5(
            $source->getPath() .
            '-thumbnailer=' . $this->thumbnailer .
            '-width=' . $this->thumbnailWidth .
            '-height=' . $this->thumbnailHeight
        );

        $filename .= '.' . $source->getExtension();

        return new File(self::CACHE_PATH, $filename);
    }

}