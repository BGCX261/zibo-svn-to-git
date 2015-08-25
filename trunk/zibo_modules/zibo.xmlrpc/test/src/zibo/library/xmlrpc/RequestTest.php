<?php

namespace zibo\library\xmlrpc;

use zibo\library\xmlrpc\exception\XmlRpcException;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class RequestTest extends BaseTestCase {

    private $methodName;
    private $request;

    protected function setUp() {
        $this->methodName = 'methodName';
        $this->request = new Request($this->methodName);
    }

    public function testConstructThrowsExceptionWhenEmptyMethodNamePassed() {
        try {
            $request = new Request('');

        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }

    public function testAddParameterThrowsExceptionWhenInvalidTypeProvided() {
        try {
            $this->request->addParameter('parameter', 'invalidType');
        } catch (XmlRpcException $e) {
            return;
        }
        $this->fail();
    }


    public function testGetXmlString() {
        $this->request->addParameter('Hello world');
        $this->request->addParameter(array('value1', 'value2'));
        $this->request->addParameter(array('minimum' => 15, 'maximum' => 25));

        $expected =
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<methodCall>' . "\n" .
            '    <methodName>' . $this->methodName . '</methodName>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '            <value>' . "\n" .
            '                <string>Hello world</string>' . "\n" .
            '            </value>' . "\n" .
            '        </param>' . "\n" .
            '        <param>' . "\n" .
            '            <value>' . "\n" .
            '                <array>' . "\n" .
            '                    <data>' . "\n" .
            '                        <value>' . "\n" .
            '                            <string>value1</string>' . "\n" .
            '                        </value>' . "\n" .
            '                        <value>' . "\n" .
            '                            <string>value2</string>' . "\n" .
            '                        </value>' . "\n" .
            '                    </data>' . "\n" .
            '                </array>' . "\n" .
            '            </value>' . "\n" .
            '        </param>' . "\n" .
            '        <param>' . "\n" .
            '            <value>' . "\n" .
            '                <struct>' . "\n" .
            '                    <member>' . "\n" .
            '                        <name>minimum</name>' . "\n" .
            '                        <value>' . "\n" .
            '                            <int>15</int>' . "\n" .
            '                        </value>' . "\n" .
            '                    </member>' . "\n" .
            '                    <member>' . "\n" .
            '                        <name>maximum</name>' . "\n" .
            '                        <value>' . "\n" .
            '                            <int>25</int>' . "\n" .
            '                        </value>' . "\n" .
            '                    </member>' . "\n" .
            '                </struct>' . "\n" .
            '            </value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodCall>';

        $this->assertXmlStringEqualsXmlString($expected, $this->request->getXmlString());
    }

}