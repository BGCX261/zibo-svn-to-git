<?php

namespace zibo\filebrowser\model\filter;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

class DirectoryFilterTest extends BaseTestCase {

	/**
     * @dataProvider providerIsAllowed
	 */
	public function testIsAllowed($expected, File $file) {
        $filter = new DirectoryFilter();
        $result = $filter->isAllowed($file);
        $this->assertEquals($expected, $result);
	}

	/**
     * @dataProvider providerIsAllowed
	 */
	public function testIsAllowedWontInclude($expected, File $file) {
        $filter = new DirectoryFilter(false);
        $result = $filter->isAllowed($file);
        $this->assertEquals(!$expected, $result);
	}

	public function providerIsAllowed() {
		return array(
            array(true, new File('/tmp')),
            array(false, new File(__FILE__)),
		);
	}

}