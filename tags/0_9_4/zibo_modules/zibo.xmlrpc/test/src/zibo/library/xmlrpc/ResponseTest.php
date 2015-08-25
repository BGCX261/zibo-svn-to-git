<?php

namespace zibo\library\xmlrpc;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

use \DOMDocument;

class ResponseTest extends BaseTestCase {

    public function testConstruct() {
        $response = new Response(new Value(15));
        $this->assertEquals(15, $response->getValue());
    }

    public function testConstructError() {
        $response = new Response(null, 1, 'Unknown method');
        $this->assertEquals(1, $response->getErrorCode());
        $this->assertEquals('Unknown method', $response->getErrorMessage());
    }

    public function testConstructWithStringElement() {
        $xml =
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<methodResponse>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '           <value><string>South Dakota</string></value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodResponse>' . "\n";

        $this->performTestConstruct($xml, 'South Dakota');
    }

    public function testConstructWithArrayElement() {
        $xml =
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<methodResponse>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '           <value>' . "\n" .
            '               <array><data>' . "\n" .
            '                   <value><string>South Dakota</string></value>' . "\n" .
            '                   <value><string>North Dakota</string></value>' . "\n" .
            '               </data></array>' . "\n" .
            '           </value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodResponse>' . "\n";

        $this->performTestConstruct($xml, array('South Dakota', 'North Dakota'));
    }

    public function testConstructWithStruct() {
        $xml =
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<methodResponse>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '           <value>' . "\n" .
            '               <struct><member>' . "\n" .
            '                   <name>test</name>' . "\n" .
            '                   <value><string>North Dakota</string></value>' . "\n" .
            '               </member></struct>' . "\n" .
            '           </value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodResponse>' . "\n";

        $this->performTestConstruct($xml, array('test' => 'North Dakota'));
    }

    public function testConstructWithErrorResponse() {
        $xml =
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<methodResponse>' . "\n" .
            '    <fault>' . "\n" .
            '       <value>' . "\n" .
            '           <struct>' . "\n" .
            '               <member>' . "\n" .
            '                   <name>faultCode</name>' . "\n" .
            '                   <value><int>4</int></value>' . "\n" .
            '               </member>' . "\n" .
            '               <member>' . "\n" .
            '                   <name>faultString</name>' . "\n" .
            '                   <value><string>Too many parameters.</string></value>' . "\n" .
            '               </member>' . "\n" .
            '           </struct>' . "\n" .
            '       </value>' . "\n" .
            '    </fault>' . "\n" .
            '</methodResponse>';

        $response = $this->performTestConstruct($xml, null);
        $this->assertEquals(4, $response->getErrorCode(), 'error code is not the expected code');
        $this->assertEquals('Too many parameters.', $response->getErrorMessage(), 'error message is not the expected message');
    }

    private function performTestConstruct($xml, $expected) {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml);

        $response = new Response($dom->documentElement);
        $this->assertEquals($expected, $response->getValue());
        return $response;
    }

    public function testGetXmlString() {
        $xml =
            '<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<methodResponse>' . "\n" .
            '    <params>' . "\n" .
            '        <param>' . "\n" .
            '            <value>' . "\n" .
            '                <string>South Dakota</string>' . "\n" .
            '            </value>' . "\n" .
            '        </param>' . "\n" .
            '    </params>' . "\n" .
            '</methodResponse>';

        $response = new Response(new Value('South Dakota'));
        $this->assertXmlStringEqualsXmlString($xml, $response->getXmlString());
    }
}