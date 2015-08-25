<?php

namespace zibo\library\xmlrpc;

use zibo\core\Zibo;

use zibo\library\xml\dom\Document;
use zibo\library\xmlrpc\exception\XmlRpcException;
use zibo\library\xmlrpc\exception\XmlRpcInvalidResponseException;
use zibo\library\String;

use zibo\ZiboException;

use \DOMDocument;
use \DOMElement;
use \Exception;

/**
 * XML-RPC response
 */
class Response {

    /**
     * Configuration key to the RNG file of a XML-RPC response
     * @var string
     */
	const CONFIG_RESPONSE_RNG = 'schema.xmlrpc.response';

	/**
	 * Error code for the response
	 * @var integer
	 */
    protected $errorCode = 0;

    /**
     * Error message of the response
     * @var string
     */
    protected $errorMessage;

    /**
     * The value
     * @var Value
     */
    protected $value;

    /**
     * Constructs a new XML-RPC response
     * @param DOMElement|Value|null $value The value of the response
     * @param integer $errorCode The error code if an error occured
     * @param string $errorMessage The error message if an error occured
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the argument combination is invalid
     */
    public function __construct($value, $errorCode = 0, $errorMessage = '') {
        if ($value instanceof DOMElement) {
            $this->parseElement($value);
        } elseif ($value instanceof Value) {
            $this->value = $value;
        } elseif ($value == null && $errorCode != 0 && !String::isEmpty($errorMessage)) {
            $this->errorCode = $errorCode;
            $this->errorMessage = $errorMessage;
        } else {
            throw new XmlRpcException('Invalid contructor call');
        }
    }

    /**
     * Gets the error code of this response
     * @return integer
     */
    public function getErrorCode() {
        return $this->errorCode;
    }

    /**
     * Gets the error message of this response
     * @return string
     */
    public function getErrorMessage() {
        return $this->errorMessage;
    }

    /**
     * Gets the actual value of this response
     * @return mixed
     */
    public function getValue() {
        if ($this->value != null) {
            return $this->value->getValue();
        }

        return null;
    }

    /**
     * Gets the XML string of this response object
     * @return string
     */
    public function getXmlString() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        if ($this->errorCode != 0) {
            $element = $this->getFaultElement($dom);
        } else {
            $element = $this->getParamsElement($dom);
        }

        $methodResponseElement = $dom->createElement('methodResponse');
        $methodResponseElement->appendChild($element);

        $dom->appendChild($methodResponseElement);

        return $dom->saveXML();
    }

    /**
     * Gets the DOM element of the error of this response
     * @param DOMDocument $dom The DOM document which is being created
     * @return DOMElement The DOM element of the error of this response
     */
    private function getFaultElement(DOMDocument $dom) {
        $errorParameter = new Value(array('faultCode' => $this->errorCode, 'faultString' => $this->errorMessage));

        $valueElement = $dom->importNode($errorParameter->getXmlElement(), true);

        $faultElement = $dom->createElement('fault');
        $faultElement->appendChild($valueElement);

        return $faultElement;
    }

    /**
     * Gets the DOM element of the result of this response
     * @param DOMDocument $dom The DOM document which is being created
     * @return DOMElement The DOM element of the result of this response
     */
    private function getParamsElement(DOMDocument $dom) {
        $valueElement = $dom->importNode($this->value->getXmlElement(), true);

        $paramElement = $dom->createElement('param');
        $paramElement->appendChild($valueElement);

        $paramsElement = $dom->createElement('params');
        $paramsElement->appendChild($paramElement);

        return $paramsElement;
    }

    /**
     * Parses a response DOM element into this object
     * @param DOMElement $responseElement The response element
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided element is not a valid response element
     */
    private function parseElement(DOMElement $responseElement) {
        if ($responseElement->tagName != 'methodResponse') {
            throw new XmlRpcException('Invalid response, methodResponse tag not found');
        }

        if ($responseElement->getElementsByTagName('fault')->length === 0) {
            $this->parseParamsElement($responseElement);
        } else {
            $this->parseFaultElement($responseElement);
        }
    }

    /**
     * Parses a error response DOM element into this object
     * @param DOMElement $responseElement The response element
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided element is not a valid response element
     */
    private function parseFaultElement(DOMElement $responseElement) {
        $faultElement = $responseElement->getElementsByTagname('fault')->item(0);

        $valueElement = $faultElement->getElementsByTagName('value')->item(0);
        if (!$valueElement) {
            throw new XmlRpcException('Invalid fault response, fault tag has no value');
        }

        $valueObject = new Value($valueElement);
        $value = $valueObject->getValue();

        $this->errorCode = $value['faultCode'];
        $this->errorMessage = $value['faultString'];
    }

    /**
     * Parses a value response DOM element into this object
     * @param DOMElement $element The response element
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided element is not a valid response element
     */
    private function parseParamsElement(DOMElement $responseElement) {
        $paramsElement = $responseElement->getElementsByTagName('params')->item(0);

        if (!$paramsElement) {
            throw new XmlRpcException('Invalid response, params tag not found');
        }

        $paramElement = $paramsElement->getElementsByTagName('param')->item(0);

        if (!$paramElement) {
            throw new XmlRpcException('Invalid response, params tag has no param');
        }

        $valueElement = $paramElement->getElementsByTagName('value')->item(0);
        if (!$valueElement) {
            throw new XmlRpcException('Invalid response, param tag has no value');
        }

        $this->value = new Value($valueElement);
    }

    /**
     * Creates a response object from the provided XML string
     * @param string $xml Response XML
     * @return Response A response object of the provided string
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided XML is empty or could not be parsed
     */
    public static function fromXMLString($xml) {
        $xml = trim($xml);
        if (empty($xml)) {
            throw new XmlRpcException('Empty response recieved', 1);
        }

        $dom = new Document('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->setRelaxNGFileFromConfig(self::CONFIG_RESPONSE_RNG);

        try {
            $dom->loadXML($xml);
        } catch (Exception $e) {
            throw new XmlRpcInvalidResponseException($xml, $e);
        }

        $element = $dom->documentElement;

        return new self($element);
    }

}