<?php

namespace zibo\library\xmlrpc;

use zibo\library\xmlrpc\exception\XmlRpcException;

use \DOMDocument;
use \DOMElement;

/**
 * Value for a XML-RPC request or response
 */
class Value {

    /**
     * Name of the boolean type
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Name of the integer type
     * @var string
     */
    const TYPE_INT = 'int';

    /**
     * Another name for the integer type
     * @var string
     */
    const TYPE_I4 = 'i4';

    /**
     * Name of the double (float) type
     * @var string
     */
    const TYPE_DOUBLE = 'double';

    /**
     * Name of the string type
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * Name of the datetime type
     * @var string
     */
    const TYPE_DATETIME = 'dateTime.iso8601';

    /**
     * Name for a base 64 encoded string
     * @var string
     */
    const TYPE_BASE64 = 'base64';

    /**
     * Name for a array type
     * @var string
     */
    const TYPE_ARRAY = 'array';

    /**
     * Name for a struct type
     * @var string
     */
    const TYPE_STRUCT = 'struct';

    /**
     * Name for a null type
     * @var string
     */
    const TYPE_NIL = 'nil';

    /**
     * The actual value
     * @var mixed
     */
    private $value;

    /**
     * The type of the value
     * @var string
     */
    private $type;

    /**
     * Constructs a new value for a XML-RPC request or response
     * @param mixed $value The actual value
     * @param string $type Name of the type of the provided value
     * @return null
     */
    public function __construct($value, $type = null) {
        $this->setValue($value, $type);
    }

    /**
     * Gets the actual value of this instance
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Sets the value of this type
     * @param mixed $value The actual value for this instance
     * @param string $type Name of the type of the value
     * @return null
     */
    private function setValue($value, $type) {
        $this->value = $value;

        if (is_null($value)) {
            $this->setType(self::TYPE_NIL);
            return;
        }

        if ($value instanceof DOMElement) {
            $this->setXmlElement($value);
        } elseif ($type != null) {
            $this->setType($type);
            // cast the internal value to the right type
            switch ($type) {
                case self::TYPE_BOOLEAN:
                    if (!is_bool($value) && strcasecmp($value, 'false') == 0) {
                        $this->value = false;
                    } else {
                        $this->value = (bool)$value;
                    }
                    break;
                case self::TYPE_INT:
                    $this->value = intval($value);
                    break;
                case self::TYPE_DOUBLE:
                    $this->value = floatval($value);
                    break;
                case self::TYPE_STRUCT:
                    if (is_object($value)) {
                        $array = array();
                        foreach ($value as $fieldName => $fieldValue) {
                            $array[$fieldName] = $fieldValue;
                        }
                        $this->value = $array;
                    } else if (!is_array($value)) {
                        throw new XmlRpcException('Unable to use ' . gettype($value) . ' as a struct');
                    }
                    break;
                case self::TYPE_NIL:
                    if (!(is_null($value) || empty($value))) {
                        $type = gettype($value);
                        if (is_scalar($value)) {
                            $type = $value . ' (' . $type . ')';
                        }
                        throw new XmlRpcException('Unable to use ' . $type . ' as a nil');
                    }
                    break;
                default:
            }

            return;
        } elseif (is_array($value) && (isset($value[0]) || empty($value))) {
            $this->setType(self::TYPE_ARRAY);
        } elseif (is_array($value) || is_object($value)) {
            $this->setType(self::TYPE_STRUCT);
            if (is_object($value)) {
                $array = array();
                foreach ($value as $fieldName => $fieldValue) {
                    $array[$fieldName] = $fieldValue;
                }
                $this->value = $array;
            }
        } elseif (is_int($value)) {
            $this->setType(self::TYPE_INT);
        } elseif (is_float($value)) {
            $this->setType(self::TYPE_DOUBLE);
        } elseif (is_bool($value)) {
            $this->setType(self::TYPE_BOOLEAN);
        } elseif (preg_match('/^\d{8}T\d{2}:\d{2}:\d{2}$/', $value)) {
            $this->setType(self::TYPE_DATETIME);
        } else {
            $this->setType(self::TYPE_STRING);
        }
    }

    /**
     * Gets the type of this value
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the type of this value
     * @param string $type Name of the type
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when a invalid type has been provided
     */
    private function setType($type) {
        if (!self::isValidType($type)) {
            throw new XmlRpcException('Invalid type provided: ' . $type);
        }

        $this->type = $type;
    }

    /**
     * Gets the DOM element for this value
     * @return DOMElement The element of this value
     */
    public function getXmlElement() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;

        $type = $this->getType();
        $valueElement = $dom->createElement('value');

        $typeElement = $dom->createElement($type);

        if ($type == self::TYPE_ARRAY) {
            $array = $this->getValue();

            $dataElement = $dom->createElement('data');
            foreach ($array as $value) {
                if ($value instanceof Value) {
                    $parameter = $value;
                } else {
                    $parameter = new self($value);
                }

                $parameterElement = $dom->importNode($parameter->getXmlElement(), true);
                $dataElement->appendChild($parameterElement);
            }

            $typeElement->appendChild($dataElement);
        } elseif ($type == self::TYPE_STRUCT) {
            $struct = $this->getValue();

            if ($struct) {
                foreach ($struct as $key => $value) {
                    $memberElement = $dom->createElement('member');
                    $typeElement->appendChild($memberElement);

                    $nameElement = $dom->createElement('name');
                    $nameElement->appendChild($dom->createTextNode($key));
                    $memberElement->appendChild($nameElement);

                    if ($value instanceof Value) {
                        $parameter = $value;
                    } else {
                        $parameter = new self($value);
                    }
                    $parameterElement = $dom->importNode($parameter->getXmlElement(), true);
                    $memberElement->appendChild($parameterElement);
                }
            } else {
                $typeElement = $dom->createElement(self::TYPE_NIL);
            }
        } elseif ($type == self::TYPE_BOOLEAN) {
            $typeElement->appendChild($dom->createTextNode($this->getValue() ? '1' : '0'));
        } elseif ($type !== self::TYPE_NIL) {
            $typeElement->appendChild($dom->createTextNode($this->getValue()));
        }

        $valueElement->appendChild($typeElement);

        $dom->appendChild($valueElement);

        return $valueElement;
    }

    /**
     * Sets the value from the provided value DOM element
     * @param DOMElement $element The DOM element of the value
     * @return null
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided element's name is not 'value'
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided element does not contain 1 child element for the type
     */
    private function setXmlElement(DOMElement $element) {
        if ($element->tagName != 'value') {
            throw new XmlRpcException('Element\'s name should be \'value\', got \'' . $element->tagName . '\'');
        }

        $children = $element->childNodes;

        if ($children->length !== 1) {
            throw new XmlRpcException('Element "value" should have exactly 1 child');
        }

        $typeElement = $children->item(0);
        $type = $typeElement->tagName;
        switch ($type) {
            case self::TYPE_STRUCT:
                $value = $this->parseStructElement($typeElement);
                break;
            case self::TYPE_ARRAY:
                $value = $this->parseArrayElement($typeElement);
                break;
            default:
                $value = $typeElement->textContent;
        }

        $this->setValue($value, $type);
    }

    /**
     * Gets the struct value from the provided value DOM element
     * @param DOMElement $element The DOM element of the value
     * @return array
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when one of the child member elements does not contain a name or value
     */
    private function parseStructElement(DOMElement $element) {
        $memberElements = $element->childNodes;

        $result = array();
        foreach ($memberElements as $memberElement) {
            $nameElement = $memberElement->getElementsByTagName('name')->item(0);
            if (!$nameElement) {
                throw new XmlRpcException('Invalid member value, no name found');
            }

            $valueElement = $memberElement->getElementsByTagName('value')->item(0);
            if (!$valueElement) {
                throw new XmlRpcException('Invalid member value, no value found');
            }

            $parameter = new Value($valueElement);
            $result[$nameElement->textContent] = $parameter->getValue();
        }

        return $result;
    }

    /**
     * Gets the array value from the provided value DOM element
     * @param DOMElement $element The DOM element of the value
     * @return array
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the provided element does not contain a data element
     */
    private function parseArrayElement(DOMElement $element) {
        $dataElement = $element->childNodes->item(0);
        if (!$dataElement) {
            throw new XmlRpcException('Invalid array value, no data found');
        }

        $valueElements = $dataElement->childNodes;

        $result = array();
        foreach ($valueElements as $valueElement) {
            $parameter = new self($valueElement);
            $result[] = $parameter->getValue();
        }

        return $result;
    }

    /**
     * Tries to convert a value from type to type
     * @param mixed $value The value to convert
     * @param string $fromType The current type of the value
     * @param string $toType The wanted type of the value
     * @return mixed The converted value
     * @throws zibo\library\xmlrpc\exception\XmlRpcException when the value could not be converted
     */
    public static function convertValue($value, $fromType, $toType) {
        switch ($toType) {
            case self::TYPE_BOOLEAN:
                return $value ? true : false;
                break;
            case self::TYPE_INT:
            case self::TYPE_I4:
                if (is_numeric($value)) {
                    return 0 + $value;
                }
                if (empty($value)) {
                    return 0;
                }
                break;
            case self::TYPE_DOUBLE:
                if (is_numeric($value)) {
                    return floatval($value);
                }
                break;
            case self::TYPE_STRING:
                return strval($value);
                break;
        }

        throw new XmlRpcException('Could not convert ' . $value . ' from ' . $fromType . ' to ' . $toType);
    }

    /**
     * Checks if the provided type is valid
     * @param string $type Type to check
     * @return boolean True if the provided type is valid, false otherwise
     */
    public static function isValidType($type) {
        if ($type != self::TYPE_BOOLEAN &&
            $type != self::TYPE_INT &&
            $type != self::TYPE_I4 &&
            $type != self::TYPE_DOUBLE &&
            $type != self::TYPE_STRING &&
            $type != self::TYPE_DATETIME &&
            $type != self::TYPE_BASE64 &&
            $type != self::TYPE_ARRAY &&
            $type != self::TYPE_STRUCT &&
            $type != self::TYPE_NIL) {
            return false;
        }
        return true;
    }

}