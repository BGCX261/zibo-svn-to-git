<?php

namespace zibo\library\xmlrpc;

use zibo\test\BaseTestCase;

use \DOMDocument;
use \DOMElement;

class ValueTest extends BaseTestCase {

    public function providerTestConstructValueAndType() {
        $base64Text = "you can't read this!";
        $base64Encoded = 'eW91IGNhbid0IHJlYWQgdGhpcyE=';
        $normalArray = array('value1', 'value2');
        $structArray = array('key1' => 'value1', 'key2' => 'value2');
        $structObject = new StructTestObject();

        return array(
           array('test', null, 'test', Value::TYPE_STRING),
           array(15, null, 15, Value::TYPE_INT),
           array(true, null, true, Value::TYPE_BOOLEAN),
           array('false', Value::TYPE_BOOLEAN, false, Value::TYPE_BOOLEAN),
           array('19980717T14:08:55', null, '19980717T14:08:55', Value::TYPE_DATETIME),
           array($base64Encoded, Value::TYPE_BASE64, $base64Encoded, Value::TYPE_BASE64),
           array($normalArray, null, $normalArray, Value::TYPE_ARRAY),
           array($structArray, null, $structArray, VALUE::TYPE_STRUCT),
           array($structObject, null, array('var1' => 'value1', 'var2' => 'value2'), Value::TYPE_STRUCT),
           array(null, null, null, Value::TYPE_NIL),
        );
    }

    /**
     * @dataProvider providerTestConstructValueAndType
     */
    public function testConstructValueAndType($phpValue, $type, $expectedValue, $expectedType) {
        $value = new Value($phpValue, $type);
        $this->assertEquals($expectedValue, $value->getValue());
        $this->assertEquals($expectedType, $value->getType());
    }

    public function testConstructWithString() {
        $value = new Value('test');
        $this->assertEquals('test', $value->getValue());
        $this->assertEquals(Value::TYPE_STRING, $value->getType());
    }

    public function testConstructWithInt() {
        $value = new Value(15);
        $this->assertEquals(15, $value->getValue());
        $this->assertEquals(Value::TYPE_INT, $value->getType());
    }

    public function testConstructWithBoolean() {
        $value = new Value(true);
        $this->assertEquals(true, $value->getValue());
        $this->assertEquals(Value::TYPE_BOOLEAN, $value->getType());
    }

    public function testConstructWithBooleanForced() {
        $value = new Value('false', 'boolean');
        $this->assertEquals(false, $value->getValue());
        $this->assertEquals(Value::TYPE_BOOLEAN, $value->getType());
    }

    public function testConstructWithDouble() {
        $value = new Value(-15.15);
        $this->assertEquals(-15.15, $value->getValue());
        $this->assertEquals(Value::TYPE_DOUBLE, $value->getType());
    }

    public function testConstructWithDateTime() {
        $date = '19980717T14:08:55';
        $value = new Value($date);
        $this->assertEquals($date, $value->getValue());
        $this->assertEquals(Value::TYPE_DATETIME, $value->getType());
    }

    public function testAddBase64Parameter() {
        $data = 'eW91IGNhbid0IHJlYWQgdGhpcyE=';
        $value = new Value($data, Value::TYPE_BASE64);
        $this->assertEquals($data, $value->getValue());
        $this->assertEquals(Value::TYPE_BASE64, $value->getType());
    }

    public function testConstructWithArray() {
        $array = array('value1', 'value2');
        $value = new Value($array);
        $this->assertEquals($array, $value->getValue());
        $this->assertEquals(Value::TYPE_ARRAY, $value->getType());
    }

    public function testConstructWithStructArray() {
        $array = array('key1' => 'value1', 'key2' => 'value2');
        $value = new Value($array);
        $this->assertEquals($array, $value->getValue());
        $this->assertEquals(Value::TYPE_STRUCT, $value->getType());
    }

    public function testConstructWithStructObject() {
        $array = array('var1' => 'value1', 'var2' => 'value2');
        $value = new Value(new StructTestObject());
        $this->assertEquals($array, $value->getValue());
        $this->assertEquals(Value::TYPE_STRUCT, $value->getType());
    }

    public function testConstructWithEmptyStruct() {
        $value = new Value(array(), Value::TYPE_STRUCT);
        $this->assertEquals(array(), $value->getValue());
        $this->assertEquals(Value::TYPE_STRUCT, $value->getType());
    }

    public function testConstructWithStringElement() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $element = $dom->createElement('value');
        $element->appendChild($dom->createElement('string', 'test'));
        $value = new Value($element);
        $this->assertEquals('test', $value->getValue());
        $this->assertEquals(Value::TYPE_STRING, $value->getType());
    }

    public function testConstructWithArrayElement() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dataElement = $dom->createElement('data');

        $valueElement = $dom->createElement('value');
        $valueElement->appendChild($dom->createElement('string', 'value1'));
        $dataElement->appendChild($valueElement);

        $valueElement = $dom->createElement('value');
        $valueElement->appendChild($dom->createElement('string', 'value2'));
        $dataElement->appendChild($valueElement);

        $arrayElement = $dom->createElement('array');
        $arrayElement->appendChild($dataElement);

        $element = $dom->createElement('value');
        $element->appendChild($arrayElement);

        $value = new Value($element);
        $this->assertEquals(array('value1', 'value2'), $value->getValue());
        $this->assertEquals(Value::TYPE_ARRAY, $value->getType());
    }

    public function testConstructWithStructElement() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $structElement = $dom->createElement('struct');

        $parameterElement = $dom->createElement('string', 'value1');
        $valueElement = $dom->createElement('value');
        $valueElement->appendChild($parameterElement);
        $memberElement = $dom->createElement('member');
        $memberElement->appendChild($dom->createElement('name', 'var1'));
        $memberElement->appendChild($valueElement);
        $structElement->appendChild($memberElement);

        $parameterElement = $dom->createElement('string', 'value2');
        $valueElement = $dom->createElement('value');
        $valueElement->appendChild($parameterElement);
        $memberElement = $dom->createElement('member');
        $memberElement->appendChild($dom->createElement('name', 'var2'));
        $memberElement->appendChild($valueElement);
        $structElement->appendChild($memberElement);

        $element = $dom->createElement('value');
        $element->appendChild($structElement);

        $value = new Value($element);
        $this->assertEquals(array('var1' => 'value1', 'var2' => 'value2'), $value->getValue());
        $this->assertEquals(Value::TYPE_STRUCT, $value->getType());
    }

    /**
     * @expectedException zibo\library\xmlrpc\exception\XmlRpcException
     **/
    public function testConstructWithInvalidTypeThrowsException() {
        $value = new Value('test', 'invalid_type_name');
    }

    public function testConstructWithStructTypeConvertsObjectToArray() {
        $test = new StructTestObject();
        $value = new Value($test, Value::TYPE_STRUCT);

        $expected = array('var1' => 'value1', 'var2' => 'value2');
        $this->assertSame($expected, $value->getValue());
    }
    /**
     * @expectedException zibo\library\xmlrpc\exception\XmlRpcException
     **/
    public function testConstructWithStructTypeThrowsExceptionWhenValueIsNotObjectOrArray() {
        $value = new Value('test', Value::TYPE_STRUCT);
    }

    public function testConstructWithBooleanTypeCastsNonBooleanToBoolean() {
        $value = new Value('0', Value::TYPE_BOOLEAN);
        $this->assertSame(false, $value->getValue());
    }

    public function testConstructWithBooleanTypeConvertsStringFalseToBooleanFalse() {
        $value = new Value('false', Value::TYPE_BOOLEAN);
        $this->assertSame(false, $value->getValue());
    }

    public function testConstructWithDoubleTypeConvertsNonFloatToFloat() {
        $value = new Value('0.50', Value::TYPE_DOUBLE);
        $this->assertSame(0.50, $value->getValue());
    }

    public function testGetXmlElementWithString() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value('test');
        $valueElement = $dom->createElement('value');
        $element = $dom->createElement('string', 'test');
        $valueElement->appendChild($element);
        $parameterElement = $value->getXmlElement();
        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXML($parameterElement));
    }

    public function testGetXmlElementWithInt() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value(15);
        $valueElement = $dom->createElement('value');
        $element = $dom->createElement('int', '15');
        $valueElement->appendChild($element);
        $parameterElement = $value->getXmlElement();
        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXML($parameterElement));
    }

    public function testGetXmlElementWithBoolean() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value(false);
        $valueElement = $dom->createElement('value');
        $element = $dom->createElement('boolean', '0');
        $valueElement->appendChild($element);
        $parameterElement = $value->getXmlElement();
        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXML($parameterElement));
    }

    public function testGetXmlElementWithDouble() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value(-15.15);
        $valueElement = $dom->createElement('value');
        $element = $dom->createElement('double', '-15.15');
        $valueElement->appendChild($element);
        $parameterElement = $value->getXmlElement();
        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXML($parameterElement));
    }

    public function testGetXmlElementWithArray() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value(array('value1', 'value2'));

        $valueElement = $dom->createElement('value');

        $dataElement = $dom->createElement('data');

        $subValueElement = $dom->createElement('value');
        $subValueElement->appendChild($dom->createElement('string', 'value1'));
        $dataElement->appendChild($subValueElement);

        $subValueElement = $dom->createElement('value');
        $subValueElement->appendChild($dom->createElement('string', 'value2'));
        $dataElement->appendChild($subValueElement);

        $element = $dom->createElement('array');
        $element->appendChild($dataElement);

        $valueElement->appendChild($element);

        $parameterElement = $value->getXmlElement();
        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXML($parameterElement));
    }

    public function testGetXMLElementWithRecursiveArrayElement() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <value>
            <array>
              <data>
                <value>
                    <array>
                        <data>
                            <value><int>1</int></value>
                            <value><int>2</int></value>
                        </data>
                    </array>
                </value>
                <value><string>Something here</string></value>
                <value><int>1</int></value>
              </data>
            </array>
        </value>';
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        $value = new Value($dom->documentElement);
        $paramElement = $value->getXmlElement();
        $this->assertXmlStringEqualsXmlString($xml, $paramElement->ownerDocument->saveXML($paramElement));
        $this->assertEqualXMLStructure($dom->documentElement, $paramElement, true);
    }

    public function testGetXmlElementWithStruct() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value(new StructTestObject);

        $valueElement = $dom->createElement('value');

        $element = $dom->createElement('struct');
        $valueElement->appendChild($element);

        $parameterElement = $dom->createElement('string', 'value1');
        $subValueElement = $dom->createElement('value');
        $subValueElement->appendChild($parameterElement);
        $memberElement = $dom->createElement('member');
        $memberElement->appendChild($dom->createElement('name', 'var1'));
        $memberElement->appendChild($subValueElement);
        $element->appendChild($memberElement);

        $parameterElement = $dom->createElement('string', 'value2');
        $subValueElement = $dom->createElement('value');
        $subValueElement->appendChild($parameterElement);
        $memberElement = $dom->createElement('member');
        $memberElement->appendChild($dom->createElement('name', 'var2'));
        $memberElement->appendChild($subValueElement);
        $element->appendChild($memberElement);

        $parameterElement = $value->getXmlElement();

        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXml($parameterElement));
    }

    public function testGetXmlElementWithEmptyStruct() {
        $dom = new DOMDocument('1.0', 'utf-8');
        $value = new Value(array(), Value::TYPE_STRUCT);

        $valueElement = $dom->createElement('value');

        $element = $dom->createElement('nil');
        $valueElement->appendChild($element);

        $parameterElement = $value->getXmlElement();

        $this->assertNotNull($parameterElement);
        $this->assertEquals($valueElement->ownerDocument->saveXML($valueElement), $parameterElement->ownerDocument->saveXml($parameterElement));
    }

}

class StructTestObject {

    public $var1 = 'value1';
    public $var2 = 'value2';

}