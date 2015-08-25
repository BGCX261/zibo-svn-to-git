<?php

namespace zibo\library\xmlrpc;

use zibo\core\Zibo;

use zibo\library\xml\dom\Document;
use zibo\library\xmlrpc\exception\XmlRpcException;
use zibo\library\String;

use \DOMDocument;
use \DOMElement;
use \Exception;

/**
 * XML-RPC request
 */
class Request {

    /**
     * Configuration key to the RNG file of a XML-RPC request
     * @var string
     */
    const CONFIG_REQUEST_RNG = 'schema.xmlrpc.request';

    /**
     * Name of the method (service) to call
     * @var string
     */
    private $methodName;

    /**
     * The parameters for the method
     * @var array
     */
    private $parameters;

    /**
     * Constructs a new XML-RPC request
     * @param string|DOMElement $value The name of the method or a DOM element which contains the full response
     * @return null
     */
    public function __construct($value) {
        $this->parameters = array();
        if ($value instanceof DOMElement) {
            $this->parseElement($value);
        } else {
            $this->setMethodName($value);
        }
    }

    /**
     * Sets the method name for the request
     * @param string $methodName
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided method name is empty
     */
    protected function setMethodName($methodName) {
        if (String::isEmpty($methodName)) {
            throw new XmlRpcException('Method name cannot be empty');
        }

        $this->methodName = $methodName;
    }

    /**
     * Gets the method name of this request
     * @return string
     */
    public function getMethodName() {
        return $this->methodName;
    }

    /**
     * Adds a parameter to this request
     * @param Value|mixed $value The value to add
     * @param string $type Type of the value
     * @return null
     */
    public function addParameter($value, $type = null) {
        if ($value instanceof Value) {
            $this->parameters[] = $value;
        } else {
            $this->parameters[] = new Value($value, $type);
        }
    }

    /**
     * Gets all the parameters for this request
     * @return array Array with Value objects
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Gets a XML string for this request
     * @return string
     */
    public function getXmlString() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        $methodCallElement = $dom->createElement('methodCall');
        $dom->appendChild($methodCallElement);

        $methodNameElement = $dom->createElement('methodName');
        $methodNameElement->appendChild($dom->createTextNode($this->getMethodName()));
        $methodCallElement->appendChild($methodNameElement);

        if (count($this->parameters) > 0) {
            $methodParametersElement = $dom->createElement('params');
            $methodCallElement->appendChild($methodParametersElement);

            foreach ($this->parameters as $parameter) {
                $parameterElement = $dom->createElement('param');
                $methodParametersElement->appendChild($parameterElement);

                $valueElement = $dom->importNode($parameter->getXmlElement(), true);
                $parameterElement->appendChild($valueElement);
            }
        }

        return $dom->saveXML();
    }

    /**
     * Parses the provided DOM element into this object
     * @param DOMElement $element DOM element of a request
     * @return null
     */
    private function parseElement(DOMElement $element) {
        if ($element->tagName != 'methodCall') {
            throw new XmlRpcException('Invalid request: methodCall tag not found');
        }
        $methodNameElement = $element->getElementsByTagName('methodName')->item(0);
        if (!$methodNameElement) {
            throw new XmlRpcException('Invalid request: methodName tag not found');
        }
        $this->setMethodName($methodNameElement->textContent);

        $paramsElement = $element->getElementsByTagName('params')->item(0);
        if (!$paramsElement) {
            return;
        }

        $paramElements = $paramsElement->getElementsByTagName('param');
        if ($paramElements->length == 0) {
            throw new XmlRpcException('Invalid request: param tag not found in params');
        }

        foreach ($paramElements as $paramElement) {
            $valueElement = $paramElement->getElementsByTagName('value')->item(0);
            if (!$valueElement) {
                throw new XmlRpcException('Invalid request: value tag not found in param');
            }

            $this->addParameter(new Value($valueElement));
        }
    }

    /**
     * Gets a request object from the provided request XML
     * @param string $xml The XML of a XML-RPC request
     * @return Request
     */
    public static function fromXMLString($xml) {
        $xml = trim($xml);
        if (empty($xml)) {
            throw new XmlRpcException('Empty request recieved', 1);
        }

        $dom = new Document('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->setRelaxNGFileFromConfig(self::CONFIG_REQUEST_RNG);

        try {
            $dom->loadXML($xml);
        } catch (Exception $exception) {
            throw new XmlRpcException($xml, 0, $exception);
        }

        $element = $dom->documentElement;

        return new self($element);
    }

}