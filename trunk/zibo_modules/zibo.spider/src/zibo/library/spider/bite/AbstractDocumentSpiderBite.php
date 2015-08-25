<?php

namespace zibo\library\spider\bite;

use zibo\library\spider\Web;
use zibo\library\spider\WebNode;
use zibo\library\xml\dom\Document;
use zibo\library\String;

/**
 * Abstract spider bite to inspect the DOM document of a node
 */
abstract class AbstractDocumentSpiderBite extends AbstractSpiderBite {

    /**
     * Inspects and processes the DOM of the prey
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $preyBaseUrl Base URL of the prey
     * @param string $preyBasePath Base path of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    public function bite(Web $web, WebNode $prey, $preyBaseUrl, $preyBasePath, Document $dom = null) {
        if (!$dom) {
            return;
        }

        $this->biteDocument($web, $prey, $preyBaseUrl, $preyBasePath, $dom);
    }

    /**
     * Hook to process the DOM
     * @param zibo\library\spider\Web $web The spider web
     * @param zibo\library\spider\WebNode $prey The current prey in the web
     * @param string $preyBaseUrl Base URL of the prey
     * @param string $preyBasePath Base path of the prey
     * @param zibo\library\xml\dom\Document $dom The DOM document of the current prey
     * @return null
     */
    abstract protected function biteDocument(Web $web, WebNode $prey, $preyBaseUrl, $preyBasePath, Document $dom);

}