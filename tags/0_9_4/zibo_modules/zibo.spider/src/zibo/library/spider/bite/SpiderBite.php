<?php

namespace zibo\library\spider\bite;

use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;

/**
 * Interface to hook in the spider
 */
interface SpiderBite {

    /**
     * Hook in the spider to parse visited HTML pages
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $preyBaseUrl Base URL of the prey
     * @param string $preyBasePath Base path of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    public function bite(Web $web, WebNode $prey, $preyBaseUrl, $preyBasePath, Document $dom = null);

}