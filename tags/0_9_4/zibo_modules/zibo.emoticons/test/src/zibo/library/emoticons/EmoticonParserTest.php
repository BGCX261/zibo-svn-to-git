<?php

namespace zibo\library\emoticons;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class EmoticonParserTest extends BaseTestCase {

	private $emoticons = array(
	   ':-)' => 'web/images/smile.png',
	   ':-D' => 'web/images/bigsmile.png',
	);

	public function testConstruct() {
		$emoticonParser = new EmoticonParser();
		$parserEmoticons = Reflection::getProperty($emoticonParser, 'emoticons');
		$this->assertFalse(empty($parserEmoticons));
	}

	public function testConstructWithEmoticons() {
		$emoticonParser = new EmoticonParser($this->emoticons);
		$parserEmoticons = Reflection::getProperty($emoticonParser, 'emoticons');
		$this->assertEquals($parserEmoticons, $this->emoticons);
	}

	public function testGetEmoticonImage() {
		$emoticonParser = new EmoticonParser($this->emoticons);
		$image = $emoticonParser->getEmoticonImage(':-)');
		$this->assertEquals($this->emoticons[':-)'], $image);
	}

	public function testSetEmoticonImage() {
		$emoticonParser = new EmoticonParser($this->emoticons);

		$emoticon = ':-(';
		$image = 'web/images/sad.png';
		$this->emoticons[$emoticon] = $image;
		$emoticonParser->setEmoticonImage($emoticon, $image);
		$parserEmoticons = Reflection::getProperty($emoticonParser, 'emoticons');

		$this->assertEquals($this->emoticons, $parserEmoticons);
	}

	/**
     * @dataProvider providerParse
	 */
	public function testParse($expected, $value) {
        $emoticonParser = new EmoticonParser($this->emoticons);
        $result = $emoticonParser->parse($value);
        $this->assertEquals($expected, $result);
	}

	public function providerParse() {
		return array(
            array('This is a test sentence.', 'This is a test sentence.'),
            array('A smile <img src="web/images/smile.png" alt=":-)" title=":-)" />', 'A smile :-)'),
            array('<img src="web/images/smile.png" alt=":-)" title=":-)" /><img src="web/images/bigsmile.png" alt=":-D" title=":-D" />', ':-):-D'),
            array('<img src="web/images/bigsmile.png" alt=":-D" title=":-D" />:p', ':-D:p'),
		);
	}

}