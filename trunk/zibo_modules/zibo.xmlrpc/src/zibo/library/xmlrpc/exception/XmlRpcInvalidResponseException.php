<?php

namespace zibo\library\xmlrpc\exception;

use zibo\ZiboException;

use \Exception;

/**
 * Exception thrown when a invalid XML-RPC response is recieved
 */
class XmlRpcInvalidResponseException extends ZiboException {

    /**
     * The string of the response, should be an XML string
     * @var string
     */
    private $responseString;

    /**
     * Constructs a new invalid response exception
     * @param string $responseString The string of the response, should be an XML string
     * @param Exception $previous The exception causing this one
     */
    public function __construct($responseString, Exception $previous = null) {
        parent::__construct('Invalid response recieved', 2, $previous);
        $this->responseString = $responseString;
    }

    /**
     * Gets the string of the response
     * @return string
     */
    public function getResponseString() {
        return $this->responseString;
    }

}