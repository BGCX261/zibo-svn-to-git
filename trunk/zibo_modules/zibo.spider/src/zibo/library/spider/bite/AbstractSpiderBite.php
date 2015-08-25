<?php

namespace zibo\library\spider\bite;

use zibo\library\String;

/**
 * Abstract spider bite with some helper functions
 */
abstract class AbstractSpiderBite implements SpiderBite {

    /**
     * Gets the absolute URL of a made reference
     * @param string $url The made reference
     * @param string $baseUrl The base URL of the node which is linking the URL
     * @param string $basePath The base path of the node which is linking the URL
     * @return string The absolute URL of the reference
     */
    protected function getAbsoluteUrl($url, $baseUrl, $basePath) {
        if (!String::looksLikeUrl($url)) {
            if (String::startsWith($url, '/')) {
                $url = rtrim($baseUrl, '/') . $url;
            } else {
                $url = $basePath . $url;
            }
        }

        return $url;
    }

}