<?php

namespace zibo\library\html;

use zibo\library\String;

use \DOMDocument;

/**
 * Parser to perform some processing on HTML
 */
class HtmlParser {

    /**
     * DOMDocument object of the html
     * @var DOMDocument
     */
    private $dom;

    /**
     * Construct this parser
     * @param string $html the html which need parsing
     * @return null
     */
    public function __construct($html) {
        $this->dom = new DOMDocument();
        $this->dom->recover = false;
        $this->dom->formatOutput = true;
        $this->dom->loadHTML($html);
    }

    /**
     * Get the parsed html
     * @return string
     */
    public function getHtml() {
        $strip = array(
            '/^<\\?xml.+\\?>/',
            '/<\\!\\[CDATA\\[/',
            '/<\\!DOCTYPE.+">/',
            '/]]>/',
        );

        $rendered = $this->dom->saveXML();
        $rendered = preg_replace($strip, '', $rendered);
        $rendered = str_replace(array('<html>', '</html>', '<body>', '</body>'), '', $rendered);
        $rendered  = trim($rendered);

        return $rendered;
    }

    /**
     * Convert the relative anchors to absolute ones
     * @param string $baseUrl base url for the relative anchors
     * @return null
     */
    public function makeAnchorsAbsolute($baseUrl) {
        $anchors = $this->dom->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $href = $anchor->getAttribute('href');
            if (!$href || String::startsWith($href, '#') || String::startsWith($href, 'http') || String::startsWith($href, 'mailto:')) {
                continue;
            }

            $anchor->setAttribute('href', $baseUrl . $href);
        }
    }

    /**
     * Convert the relative images to absolute ones
     * @param string $baseUrl base url for the relative images
     * @return null
     */
    public function makeImagesAbsolute($baseUrl) {
        $images = $this->dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $src = $image->getAttribute('src');
            if (String::startsWith($src, 'http://')) {
                continue;
            }

            $image->setAttribute('src', $baseUrl . $src);
        }
    }

}