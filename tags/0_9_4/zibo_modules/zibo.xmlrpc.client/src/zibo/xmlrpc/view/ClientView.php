<?php

namespace zibo\xmlrpc\view;

use zibo\admin\view\BaseView;

use zibo\library\xmlrpc\Response;
use zibo\library\xmlrpc\Request;
use zibo\library\String;

use zibo\xmlrpc\form\ClientForm;
use zibo\xmlrpc\parser\ParameterParser;

/**
 * View for XML-RPC client
 */
class ClientView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'xmlrpc/client';

    /**
     * Path to the style of this view
     * @var string
     */
    const STYLE = 'web/styles/xmlrpc/client.css';

    /**
     * Constructs a new XML-RPC client view
     * @param zibo\xmlrpc\form\ClientForm $form The form of the client
     * @param zibo\library\xmlrpc\Request $request
     * @param zibo\library\xmlrpc\Response $response
     * @param string $responseString
     * @param float $time Time spent on the request in seconds
     * @return null
     */
    public function __construct(ClientForm $form, Request $request = null, Response $response = null, $responseString = null, $time = null) {
    	parent::__construct(self::TEMPLATE);

    	$this->set('form', $form);
    	$this->set('time', $time);

	    $this->set('requestString', null);
	    $this->set('responseString', null);

    	if ($request) {
    	   $this->set('requestString', String::addLineNumbers(trim($request->getXmlString())));
    	}

    	if ($responseString && !$response) {
    	   $this->set('responseString', String::addLineNumbers(trim($responseString)));
    	} elseif ($response) {
            $this->set('responseString', String::addLineNumbers(trim($response->getXmlString())));
    	    $errorCode = $response->getErrorCode();
    	    if ($errorCode) {
    	        $this->set('error', $errorCode . ': ' . $response->getErrorMessage());
    	    } else {
    	        $parser = new ParameterParser();
    	        $result = $parser->unparse($response->getValue(), true);
    	        $this->set('result', $result);
    	    }
    	}

    	$this->addStyle(self::STYLE);
    }

}
