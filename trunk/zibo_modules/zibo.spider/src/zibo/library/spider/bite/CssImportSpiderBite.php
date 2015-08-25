<?php

namespace zibo\library\spider\bite;

use zibo\library\optimizer\cssmin\CSSMin;
use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;

/**
 * Spider bite to gt all the imported CSS stylesheets from a CSS stylesheet
 */
class CssImportSpiderBite extends AbstractSpiderBite {

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

        $urls = $this->getImportUrlsFromStyle($source, $baseUrl, $preyBaseUrl);
        foreach ($urls as $url) {
            $link = $web->getNode($url);
            $link->addType(WebNode::TYPE_CSS);
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
    private function getImportUrlsFromStyle($source, $baseUrl, $preyBaseUrl) {
        $urls = array();

        $source = preg_replace(CSSMin::REGEX_COMMENT, '', $source);

        $lines = explode("\n", $source);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            if (!preg_match(CSSMin::REGEX_IMPORT, $line)) {
                break;
            }

            $importFileName = $this->getFileNameFromImportLine($line);
            $importUrl = $this->getAbsoluteUrl($importFileName, $baseUrl, $preyBaseUrl);

            $urls[$importUrl] = $importUrl;
        }

        return $urls;
    }

    /**
     * Extracts the file name of a CSS import statement
     * @param string $line Line of the import statement
     * @return string File name referenced in the import statement
     */
    private function getFileNameFromImportLine($line) {
        $line = str_replace(array('@import', ';'), '', $line);
        $line = trim($line);

        if (strpos($line, ' ') !== false) {
            list($fileToken, $mediaToken) = explode(' ', $line, 2);
        } else {
            $fileToken = $line;
        }

        if (preg_match('/^url/', $fileToken)) {
            $fileToken = substr($fileToken, 3);
        }

        return str_replace(array('(', '"', '\'', ')'), '', $fileToken);
    }

}