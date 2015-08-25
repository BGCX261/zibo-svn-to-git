<?php

namespace zibo\filebrowser\model\filter;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

class ExtensionFilterTest extends BaseTestCase {

	/**
     * @dataProvider providerIsAllowed
	 */
	public function testIsAllowed($expected, File $file) {
        $filter = new ExtensionFilter(array('jpg', 'png'));
        $result = $filter->isAllowed($file);
        $this->assertEquals($expected, $result);
	}

	/**
     * @dataProvider providerIsAllowed
	 */
	public function testIsAllowedWontInclude($expected, File $file) {
        $filter = new ExtensionFilter(array('jpg', 'png'), false);
        $result = $filter->isAllowed($file);
        $this->assertEquals(!$expected, $result);
	}

	public function providerIsAllowed() {
		return array(
            array(true, new File('/tmp/test.jpg')),
            array(false, new File(__FILE__)),
		);
	}

}