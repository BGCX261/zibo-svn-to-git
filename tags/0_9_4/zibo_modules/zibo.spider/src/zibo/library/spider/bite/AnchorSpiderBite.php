<?php

namespace zibo\library\spider\bite;

use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;
use zibo\library\String;

/**
 * Spider bite to get all the anchors from a page
 */
class AnchorSpiderBite extends AbstractDocumentSpiderBite {

    /**
     * Adds the URL's from the anchors in the page to the web
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $baseUrl Base URL of the crawl
     * @param string $preyBaseUrl Base URL of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    protected function biteDocument(Web $web, WebNode $prey, $baseUrl, $preyBaseUrl, Document $dom) {
        $anchors = $dom->getElementsByTagName('a');
        foreach ($anchors as $anchor) {
            $url = $anchor->getAttribute('href');

            if (!$url || String::startsWith($url, '#')) {
                continue;
            }

            if (!String::startsWith($url, 'mailto:')) {
                $url = $this->getAbsoluteUrl($url, $baseUrl, $preyBaseUrl);
            }

            $node = $web->getNode($url);
            $node->addReference($prey);

            $prey->addLink($node);
        }
    }

}