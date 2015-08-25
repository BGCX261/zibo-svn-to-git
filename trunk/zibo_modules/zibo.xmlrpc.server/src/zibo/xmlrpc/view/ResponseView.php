<?php

namespace zibo\xmlrpc\view;

use zibo\core\View;

use zibo\library\xmlrpc\Response;

class ResponseView implements View {

    private $response;

    public function __construct(Response $response) {
        $this->response = $response;
    }

    public function render($return = true) {
        $xml = $this->response->getXmlString();

        if ($return) {
            return $xml;
        }

        echo $xml;

        return;
    }

}
