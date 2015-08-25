<?php

namespace zibo\library\spider\bite;

use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;

/**
 * Spider bite to get all the images of a page
 */
class ImageSpiderBite extends AbstractDocumentSpiderBite {

    /**
     * Adds all the images from the page to the web
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $baseUrl Base URL of the crawl
     * @param string $preyBaseUrl Base URL of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    protected function biteDocument(Web $web, WebNode $prey, $baseUrl, $preyBaseUrl, Document $dom) {
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $image) {
            $url = $image->getAttribute('src');

            if (!$url) {
                continue;
            }

            $url = $this->getAbsoluteUrl($url, $baseUrl, $preyBaseUrl);

            $link = $web->getNode($url);
            $link->addType(WebNode::TYPE_IMAGE);
            $link->addReference($prey);

            $prey->addLink($link);
        }
    }

}