<?php

namespace zibo\library\xml\dom;

use zibo\test\BaseTestCase;

class DocumentTest extends BaseTestCase {

    protected function setUp() {
        $this->setUpApplication(__DIR__ . '/../../../../../application');
    }

    protected function tearDown() {
        $this->tearDownApplication();
    }

    /**
     * @expectedException zibo\library\xml\exception\LibxmlException
     */
    public function testLoadXMLThrowsLibxmlExceptionOnInvalidXML() {
        $doc = new Document('1.0', 'utf-8');
        $doc->loadXML('<?xml version="1.0" encoding="UTF-8"?><root><invalid></root>');
    }

    /**
     * @expectedException zibo\library\xml\exception\LibxmlException
     */
    public function testLoadThrowsLibxmlExceptionOnInvalidXMLFile() {
        $doc = new Document('1.0', 'utf-8');
        $doc->load('application/data/test_invalid.xml');
    }


    public function testLoadWithValidXMLFile() {
        $doc = new Document('1.0', 'utf-8');
        $doc->load('application/data/test_valid.xml');
    }

    public function testLoadXMLWithValidXML() {
        $doc = new Document('1.0', 'utf-8');
        $doc->loadXML('<?xml version="1.0" encoding="UTF-8"?><root><valid/></root>');
    }
}
