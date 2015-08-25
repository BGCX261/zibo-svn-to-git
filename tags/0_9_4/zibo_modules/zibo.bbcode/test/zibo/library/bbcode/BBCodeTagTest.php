<?php

namespace zibo\library\bbcode;

use zibo\test\BaseTestCase;

class BBCodeTagTest extends BaseTestCase {

	/**
     * @dataProvider providerContructWithValidContent
	 */
	public function testConstructWithValidContent($name, $parameters, $isCloseTag, $content) {
	    $tag = new BBCodeTag($content);

	    $this->assertEquals($name, $tag->getTagName());
	    $this->assertEquals($parameters, $tag->getParameters());
	    $this->assertEquals($isCloseTag, $tag->isCloseTag());
	}

	public function providerContructWithValidContent() {
		return array(
            array('b', array(), false, 'b'),
            array('i', array(), false, 'I'),
            array('img', array(), true, '/img'),
            array(
                'url',
                array(0 => 'http://www.google.com'),
                false,
                'url=http://www.google.com'
            ),
            array(
                'img',
                array('width' => '150', 'height' => '100'),
                false,
                'img width=150 height=100'
            ),
            array(
                'img',
                array('width' => '150', 'alt' => 'let\'s test a string parameter', 'height' => '100'),
                false,
                'img width=150 alt="let\'s test a string parameter" height=100'
            ),
		);
	}

	/**
     * @dataProvider providerContructWithInvalidContent
     * @expectedException zibo\ZiboException
	 */
	public function testConstructWithInvalidContent($content) {
	    new BBCodeTag($content);
	}

	public function providerContructWithInvalidContent() {
		return array(
            array(null),
            array(''),
            array($this),
		);
	}

}