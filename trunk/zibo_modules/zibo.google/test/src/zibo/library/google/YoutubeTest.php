<?php

namespace zibo\library\google;

use zibo\test\BaseTestCase;
use zibo\test\Reflection;

class YoutubeTest extends BaseTestCase {

	/**
     * @dataProvider providerConstruct
	 */
	public function testConstruct($expectedId, $url) {
		$youtube = new Youtube($url);
		$this->assertEquals(Youtube::DEFAULT_HEIGHT, Reflection::getProperty($youtube, 'height'));
		$this->assertEquals(Youtube::DEFAULT_WIDTH, Reflection::getProperty($youtube, 'width'));
		$this->assertEquals($expectedId, $youtube->getVideoId());
	}

	public function providerConstruct() {
        return array(
            array('5DBTtC4J0OY', '5DBTtC4J0OY'),
            array('5DBTtC4J0OY', 'http://www.youtube.com/watch?v=5DBTtC4J0OY'),
            array('5DBTtC4J0OY', 'http://www.youtube.com/v/5DBTtC4J0OY'),
            array('5DBTtC4J0OY', 'http://www.youtube.com/v/5DBTtC4J0OY&hl=en'),
        );
	}

	/**
     * @dataProvider providerConstructThrowsExceptionWhenInvalidUrlPassed
     * @expectedException zibo\ZiboException
	 */
	public function testConstructThrowsExceptionWhenInvalidUrlPassed($url) {
		new Youtube($url);
	}

	public function providerConstructThrowsExceptionWhenInvalidUrlPassed() {
        return array(
            array(''),
            array($this),
            array('www.test.be/v/5DBTtC4J0OY'),
            array('http://www.test.be/v/5DBTtC4J0OY'),
        );
	}

}