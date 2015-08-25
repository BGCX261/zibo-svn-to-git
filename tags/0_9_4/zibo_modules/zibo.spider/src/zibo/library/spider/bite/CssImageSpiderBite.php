<?php

namespace zibo\library\spider\bite;

use zibo\library\optimizer\cssmin\CSSMin;
use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;

/**
 * Spider bite to gt all the imported CSS stylesheets from a CSS stylesheet
 */
class CssImageSpiderBite extends AbstractSpiderBite {

    /**
     * Adds the imported style sheets to the web
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $baseUrl Base URL of the crawl
     * @param string $preyBaseUrl Base URL of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    public function bite(Web $web, WebNode $prey, $baseUrl, $preyBaseUrl, Document $dom = null) {
        if (!$prey->hasType(WebNode::TYPE_CSS)) {
            return;
        }

        $response = $prey->getResponse();

        if (!$response || $response->getResponseCode() != 200) {
            return;
        }

        $source = $response->getContent();
        if (!$source) {
            return;
        }

        $urls = $this->getImageUrlsFromStyle($source, $baseUrl, $preyBaseUrl);
        foreach ($urls as $url) {
            $link = $web->getNode($url);
            $link->addType(WebNode::TYPE_IMAGE);
            $link->addReference($prey);

            $prey->addLink($link);
        }
    }

    /**
     * Gets all the imports from the provided CSS source
     * @param string $source CSS source
     * @param string $baseUrl Base URL of the crawl
     * @param string $preyBaseUrl Base URL of the prey
     * @return array Array with the URLs of the imports as key and value
     */
    private function getImageUrlsFromStyle($source, $baseUrl, $preyBaseUrl) {
        $cssMin = new CSSMin();

        $source = preg_replace(CSSMin::REGEX_IMPORT, '', $source);
        $source = $cssMin->minify($source, true);

        $urlMatches = array();
        preg_replace_callback(
            '/url( )?\\(["\']?([^;\\\\"\')]*)(["\']?)\\)([^;\\)]*);/',
            function ($matches) use (&$urlMatches) {
                $urlMatches[] = $matches[2];

                return '';
            },
            $source
        );

        $urls = array();
        foreach ($urlMatches as $url) {
            $url = $this->getAbsoluteUrl($url, $baseUrl, $preyBaseUrl);
            $urls[$url] = $url;
        }

        return $urls;
    }

}