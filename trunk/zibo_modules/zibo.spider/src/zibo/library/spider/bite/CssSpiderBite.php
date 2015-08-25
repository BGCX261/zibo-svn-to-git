<?php

namespace zibo\library\spider\bite;

use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;

/**
 * Spider bite to gt all the CSS stylesheets from a page
 */
class CssSpiderBite extends AbstractDocumentSpiderBite {

    /**
     * Adds the used style sheets to the web
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $baseUrl Base URL of the crawl
     * @param string $preyBaseUrl Base URL of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    protected function biteDocument(Web $web, WebNode $prey, $baseUrl, $preyBaseUrl, Document $dom) {
        $links = $dom->getElementsByTagName('link');
        foreach ($links as $link) {
            $type = $link->getAttribute('type');
            $rel = $link->getAttribute('rel');
            $url = $link->getAttribute('href');

            if ($type != 'text/css' || $rel != 'stylesheet' || !$url) {
                continue;
            }

            $url = $this->getAbsoluteUrl($url, $baseUrl, $preyBaseUrl);

            $link = $web->getNode($url);
            $link->addType(WebNode::TYPE_CSS);
            $link->addReference($prey);

            $prey->addLink($link);
        }
    }

}