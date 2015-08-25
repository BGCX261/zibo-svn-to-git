<?php

namespace zibo\library\bbcode\tag;

use zibo\library\html\Image;
use zibo\library\String;

/**
 * Implementation of the image tag
 */
class ImageTag extends AbstractTag {

    /**
     * Name of the tag
     * @var string
     */
    const NAME = 'img';

    /**
     * Constructs the url tag
     * @return null
     */
    public function __construct() {
        parent::__construct(self::NAME, true, false);
    }

    /**
     * Parses the tag
     * @param string $content Content of the tag
     * @param array $parameters Parameters of the tag
     * @return string HTML replacement for the tag
     */
    public function parseTag($content, array $parameters) {
        if (!$content || !String::looksLikeUrl($content)) {
            return false;
        }

        $width = null;
        $height = null;
        $alt = null;

        if (array_key_exists(0, $parameters) && strpos($parameters[0], 'x')) {
            // [img=<width>x<height>]url[/img]
            list($width, $height) = explode($parameters[0]);
        } else {
            if (array_key_exists('width', $parameters)) {
                // [img width=<width>]url[/img]
                $width = $parameters['width'];
            } elseif (array_key_exists('w', $parameters)) {
                // [img w=<width>]url[/img]
                $width = $parameters['w'];
            }

            if (array_key_exists('height', $parameters)) {
                // [img height=<height>]url[/img]
                $height = $parameters['height'];
            } elseif (array_key_exists('h', $parameters)) {
                // [img h=<height>]url[/img]
                $height = $parameters['h'];
            }
        }

        if (array_key_exists('alt', $parameters)) {
            $alt = $parameters['alt'];
        }

        return $this->getImageHtml($content, $width, $height, $alt);
    }

    /**
     * Gets the HTML for the image
     * @param string $src The URL to the image
     * @param integer $width The width in pixels
     * @param integer $height The height in pixels
     * @param string $alt The alternative text for the image
     * @return string The HTML of the image
     */
    private function getImageHtml($src, $width = 0, $height = 0, $alt = null) {
        $image = new Image($src);

        if ($width) {
            $image->setAttribute('width', $width);
        }
        if ($height) {
            $image->setAttribute('height', $height);
        }
        if ($alt) {
            $image->setAttribute('alt', $alt);
        }

        return $image->getHtml();
    }

}